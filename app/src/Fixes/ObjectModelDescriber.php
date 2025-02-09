<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EDM\Fixes;

use Doctrine\Common\Annotations\Reader;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareInterface;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareTrait;
use Nelmio\ApiDocBundle\Model\Model;
use Nelmio\ApiDocBundle\ModelDescriber\Annotations\AnnotationsReader;
use Nelmio\ApiDocBundle\ModelDescriber\ApplyOpenApiDiscriminatorTrait;
use Nelmio\ApiDocBundle\ModelDescriber\ModelDescriberInterface;
use Nelmio\ApiDocBundle\OpenApiPhp\Util;
use Nelmio\ApiDocBundle\PropertyDescriber\PropertyDescriberInterface;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class ObjectModelDescriber implements ModelDescriberInterface, ModelRegistryAwareInterface
{
    use ModelRegistryAwareTrait;
    use ApplyOpenApiDiscriminatorTrait;

    private PropertyInfoExtractorInterface $propertyInfo;
    private ?ClassMetadataFactoryInterface $classMetadataFactory;
    private ?Reader $doctrineReader;
    /** @var PropertyDescriberInterface|PropertyDescriberInterface[] */
    private $propertyDescriber;
    /** @var string[] */
    private array $mediaTypes;
    /** @var (NameConverterInterface&AdvancedNameConverterInterface)|null */
    private ?NameConverterInterface $nameConverter;
    private bool $useValidationGroups;

    /**
     * @param PropertyDescriberInterface|PropertyDescriberInterface[]      $propertyDescribers
     * @param (NameConverterInterface&AdvancedNameConverterInterface)|null $nameConverter
     * @param string[]                                                     $mediaTypes
     */
    public function __construct(
        PropertyInfoExtractorInterface $propertyInfo,
        ?Reader $reader,
        $propertyDescribers,
        array $mediaTypes,
        ?NameConverterInterface $nameConverter = null,
        bool $useValidationGroups = false,
        ?ClassMetadataFactoryInterface $classMetadataFactory = null
    ) {
        if (is_iterable($propertyDescribers)) {
            trigger_deprecation('nelmio/api-doc-bundle', '4.17', 'Passing an array of PropertyDescriberInterface to %s() is deprecated. Pass a single PropertyDescriberInterface instead.', __METHOD__);
        } else {
            if (!$propertyDescribers instanceof PropertyDescriberInterface) {
                throw new \InvalidArgumentException(sprintf('Argument 3 passed to %s() must be an array of %s or a single %s.', __METHOD__, PropertyDescriberInterface::class, PropertyDescriberInterface::class));
            }
        }

        $this->propertyInfo = $propertyInfo;
        $this->doctrineReader = $reader;
        $this->propertyDescriber = $propertyDescribers;
        $this->mediaTypes = $mediaTypes;
        $this->nameConverter = $nameConverter;
        $this->useValidationGroups = $useValidationGroups;
        $this->classMetadataFactory = $classMetadataFactory;
    }

    public function describe(Model $model, OA\Schema $schema)
    {
        $class = $model->getType()->getClassName();
        $schema->_context->class = $class;

        $context = ['serializer_groups' => null];
        if (null !== $model->getGroups()) {
            $context['serializer_groups'] = array_filter($model->getGroups(), 'is_string');
        }

        $reflClass = new \ReflectionClass($class);
        $annotationsReader = new AnnotationsReader(
            $this->doctrineReader,
            $this->modelRegistry,
            $this->mediaTypes,
            $this->useValidationGroups
        );
        $classResult = $annotationsReader->updateDefinition($reflClass, $schema);

        if (!$classResult->shouldDescribeModelProperties()) {
            return;
        }

        $schema->type = 'object';

        $mapping = false;
        if (null !== $this->classMetadataFactory) {
            $mapping = $this->classMetadataFactory
                ->getMetadataFor($class)
                ->getClassDiscriminatorMapping();
        }

        if ($mapping && Generator::UNDEFINED === $schema->discriminator) {
            $this->applyOpenApiDiscriminator(
                $model,
                $schema,
                $this->modelRegistry,
                $mapping->getTypeProperty(),
                $mapping->getTypesMapping()
            );
        }

        $propertyInfoProperties = $this->propertyInfo->getProperties($class, $context);

        if (null === $propertyInfoProperties) {
            return;
        }

        $defaultValues = array_filter($reflClass->getDefaultProperties(), static function ($value) {
            return null !== $value;
        });

        // Fix for https://github.com/nelmio/NelmioApiDocBundle/issues/2222
        // Promoted properties with a value initialized by the constructor are not considered to have a default value
        // and are therefore not returned by ReflectionClass::getDefaultProperties(); see https://bugs.php.net/bug.php?id=81386
        $reflClassConstructor = $reflClass->getConstructor();
        $reflClassConstructorParameters = null !== $reflClassConstructor ? $reflClassConstructor->getParameters() : [];
        foreach ($reflClassConstructorParameters as $parameter) {
            if (!$parameter->isDefaultValueAvailable()) {
                continue;
            }

            $defaultValues[$parameter->name] = $parameter->getDefaultValue();
        }

        foreach ($propertyInfoProperties as $propertyName) {
            $serializedName = null !== $this->nameConverter ? $this->nameConverter->normalize($propertyName, $class, null, $model->getSerializationContext()) : $propertyName;

            $reflections = $this->getReflections($reflClass, $propertyName);

            // Check if a custom name is set
            foreach ($reflections as $reflection) {
                $serializedName = $annotationsReader->getPropertyName($reflection, $serializedName);
            }

            $property = Util::getProperty($schema, $serializedName);

            // Fix for https://github.com/nelmio/NelmioApiDocBundle/issues/2222
            // Property default value has to be set before SymfonyConstraintAnnotationReader::processPropertyAnnotations()
            // is called to prevent wrongly detected required properties
            if (Generator::UNDEFINED === $property->default && array_key_exists($propertyName, $defaultValues)) {
                $property->default = $defaultValues[$propertyName];
            }

            // Interpret additional options
            $groups = $model->getGroups();
            if (isset($groups[$propertyName]) && is_array($groups[$propertyName])) {
                $groups = $model->getGroups()[$propertyName];
            }
            foreach ($reflections as $reflection) {
                $annotationsReader->updateProperty($reflection, $property, $groups);
            }

            // If type manually defined
            if (Generator::UNDEFINED !== $property->type || Generator::UNDEFINED !== $property->ref) {
                continue;
            }

            $types = $this->propertyInfo->getTypes($class, $propertyName);
            if (null === $types || 0 === count($types)) {
                throw new \LogicException(sprintf('The PropertyInfo component was not able to guess the type of %s::$%s. You may need to add a `@var` annotation or use `@OA\Property(type="")` to make its type explicit.', $class, $propertyName));
            }

            $this->describeProperty($types, $model, $property, $propertyName, $schema);
        }
    }

    /**
     * @return \ReflectionProperty[]|\ReflectionMethod[]
     */
    private function getReflections(\ReflectionClass $reflClass, string $propertyName): array
    {
        $reflections = [];
        if ($reflClass->hasProperty($propertyName)) {
            $reflections[] = $reflClass->getProperty($propertyName);
        }

        $camelProp = $this->camelize($propertyName);
        foreach (['', 'get', 'is', 'has', 'can', 'add', 'remove', 'set'] as $prefix) {
            if ($reflClass->hasMethod($prefix.$camelProp)) {
                $reflections[] = $reflClass->getMethod($prefix.$camelProp);
            }
        }

        return $reflections;
    }

    /**
     * Camelizes a given string.
     */
    private function camelize(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * @param Type[] $types
     */
    private function describeProperty(array $types, Model $model, OA\Schema $property, string $propertyName, OA\Schema $schema): void
    {
        $propertyDescribers = is_iterable($this->propertyDescriber) ? $this->propertyDescriber : [$this->propertyDescriber];

        foreach ($propertyDescribers as $propertyDescriber) {
            if ($propertyDescriber instanceof ModelRegistryAwareInterface) {
                $propertyDescriber->setModelRegistry($this->modelRegistry);
            }
            if ($propertyDescriber->supports($types)) {
                $propertyDescriber->describe($types, $property, $model->getGroups(), $schema, $model->getSerializationContext());

                return;
            }
        }

        throw new \Exception(sprintf('Type "%s" is not supported in %s::$%s. You may use the `@OA\Property(type="")` annotation to specify it manually.', $types[0]->getBuiltinType(), $model->getType()->getClassName(), $propertyName));
    }

    public function supports(Model $model): bool
    {
        return Type::BUILTIN_TYPE_OBJECT === $model->getType()->getBuiltinType()
            && (class_exists($model->getType()->getClassName()) || interface_exists($model->getType()->getClassName()));
    }
}
