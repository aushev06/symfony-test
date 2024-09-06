<?php

namespace EDM\Controllers;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

use EDM\EntityEnhancements\EntityEmbeddedInterface;
use EDM\Enums\CRUDAction;
use EDM\Access;
use EDM\Enums\UserType;
use EDM\Exceptions\InvalidInputException;
use EDM\Exceptions\NotFoundException;
use EDM\Exceptions\ValidationFailedException;
use EDM\Responses\RestEntityResponse;
use EDM\Responses\RestListResponse;
use EDM\Responses\RestQueryResponse;
use EDM\Tools\Input\InputBagHandler;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractCRUDController extends AbstractController
{
	public const ENTITY_CLASS = 'ENTITY_CLASS_NOT_DEFINED';
	public const ENTITY_ALIAS = RestQueryResponse::DEFAULT_ALIAS;

	protected const GROUPS_CREATE = [Access::REGULAR, Access::MANAGER, Access::CREATE, Access::REGULAR_CREATE, Access::MANAGER_CREATE];
	protected const GROUPS_READ   = [Access::REGULAR, Access::MANAGER, Access::READ, Access::REGULAR_READ, Access::MANAGER_READ, Access::EMBEDDED];
	protected const GROUPS_UPDATE = [Access::REGULAR, Access::MANAGER, Access::UPDATE, Access::REGULAR_UPDATE, Access::MANAGER_UPDATE];
	
	public function __construct(
		private readonly EntityManagerInterface $entity_manager,
		private readonly SerializerInterface $serializer,
		private readonly ValidatorInterface $validator,
		protected readonly RequestStack $request_stack,
	)	{	}

	protected function getManager(): EntityManagerInterface
	{
		return $this->entity_manager;
	}

	protected function getSerializer(): SerializerInterface
	{
		return $this->serializer;
	}

	protected function getValidator(): ValidatorInterface
	{
		return $this->validator;
	}

	protected function getRepository(?string $class = null): EntityRepository
	{
		return $this->entity_manager->getRepository($class ?? static::ENTITY_CLASS);
	}

	protected function getCurrentRequest(): Request
	{
		return $this->request_stack->getCurrentRequest();
	}

	protected function composeGroups(CRUDAction $action): array
	{
		$groups = [];
		$groups[] = Access::REGULAR;
		$groups[] = Access::MANAGER;
		switch ($action) {
			case CRUDAction::List:
			case CRUDAction::Read:
				$groups[] = Access::READ;
				$groups[] = Access::REGULAR_READ;
				$groups[] = Access::MANAGER_READ;
				break;

			case CRUDAction::Create:
				$groups[] = Access::CREATE;
				$groups[] = Access::REGULAR_CREATE;
				$groups[] = Access::MANAGER_CREATE;
				break;

			case CRUDAction::Update:
				$groups[] = Access::UPDATE;
				$groups[] = Access::REGULAR_UPDATE;
				$groups[] = Access::MANAGER_UPDATE;
				break;

			case CRUDAction::Delete:
				break;
		}

		return $groups;
	}

	protected function getRequestData(): array
	{
		try {
			$data = json_decode($this->getCurrentRequest()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
		} catch (\Exception $exception) {
			throw new BadRequestException('Invalid JSON provided.', 400, $exception);
		}
		if (is_array($data) == false) {
			throw new BadRequestException('Invalid JSON provided.');
		}
		return $data;
	}

	protected function lookupEntity(mixed $id, ?string $class = null): ?object
	{
		return $this->entity_manager->find($class ?? static::ENTITY_CLASS, $id);
	}

	protected function listEntities(?string $class = null, ?string $alias = null): RestListResponse
	{
		$request = $this->getCurrentRequest();

//		$trace = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS,2)[1];
//		$reflection = new \ReflectionMethod($trace['class'], $trace['function']);
//		foreach ($reflection->getAttributes(OA\Parameter::class) as $attribute) {
//			/** @var OA\Parameter $parameter */
//			$parameter = $attribute->newInstance();
//
//			$value = $request->query->get($parameter->name);
//		}

		if ($class === null) {
			$class = static::ENTITY_CLASS;
		}
		if ($alias === null) {
			$alias = static::ENTITY_ALIAS;
		}

		$query = $this->entity_manager->createQueryBuilder()
			->select($alias)
			->from($class, $alias);

		$groups = $this->composeGroups(CRUDAction::Read);

		$input = new InputBagHandler($request->query);

		if (is_subclass_of($class, EntityEmbeddedInterface::class) && $allowed_embedded = $class::allowedEmbedded()) {
			foreach ($input->readStringArray(Access::EMBEDDED) as $index => $embedded) {
				if (in_array($embedded, $allowed_embedded) == false) {
					continue;
				}
				$groups[] = Access::embedded($embedded);
				$embedded_alias = 'em' . $index;
				$query->addSelect($embedded_alias)->leftJoin($alias . '.' . $embedded, $embedded_alias);
			}
		}

		foreach ($input->readStringArray(Access::EXTRA) as $extra) {
			$groups[] = Access::extra($extra);
		}

		return (new RestListResponse($query))
			->setDefaultAlias($alias)
			->setSerializer($this->serializer)
			->setNormalizationGroups($groups);
	}

	protected function validateEntity(object $entity, ?array $groups = null): void
	{
		$validator = $this->getValidator();
		$violations = $validator->validate($entity, null, $groups);
		if ($violations->count()) {
			throw new ValidationFailedException($violations);
		}
	}

	protected function flushEntity(object $entity): void
	{
		$this->entity_manager->persist($entity);
		try {
			$this->entity_manager->flush();
		} catch (UniqueConstraintViolationException) {
			throw new InvalidInputException('There is already a record with similar key parameters.');
		}
	}

	protected function createEntity(?string $class = null, ?callable $callback = null): RestEntityResponse
	{
		$groups = $this->composeGroups(CRUDAction::Create);
		$entity = $this->serializer->denormalize($this->getRequestData(), $class ?? static::ENTITY_CLASS, 'json', [
			AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
			AbstractNormalizer::GROUPS => $groups
		]);
		if (is_callable($callback)) {
			$callback($entity, CRUDAction::Create, $class, $groups);
		}
		$this->validateEntity($entity, $groups);
		$this->flushEntity($entity);
		return (new RestEntityResponse($entity))
			->setSerializer($this->serializer)
			->setNormalizationGroups($this->composeGroups(CRUDAction::Read));
	}

	protected function readEntity(mixed $id, ?string $class = null): RestEntityResponse
	{
		$entity = $this->lookupEntity($id, $class);
		if ($entity == null) {
			throw new NotFoundException(null, $id);
		}
		return (new RestEntityResponse($entity))
			->setSerializer($this->serializer)
			->setNormalizationGroups($this->composeGroups(CRUDAction::Read));
	}

	protected function updateEntity(mixed $id, ?string $class = null, ?callable $callback = null): RestEntityResponse
	{
		$entity = $this->lookupEntity($id, $class);
		if ($entity == null) {
			throw new NotFoundException(null, $id);
		}
		$groups = $this->composeGroups(CRUDAction::Update);
		$this->serializer->denormalize($this->getRequestData(), $class ?? static::ENTITY_CLASS, 'json', [
			AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
			AbstractNormalizer::GROUPS => $groups,
			AbstractNormalizer::OBJECT_TO_POPULATE => $entity
		]);
		if (is_callable($callback)) {
			$callback($entity, CRUDAction::Update, $class, $groups);
		}
		$this->validateEntity($entity, $groups);
		$this->flushEntity($entity);
		return (new RestEntityResponse($entity))
			->setSerializer($this->serializer)
			->setNormalizationGroups($this->composeGroups(CRUDAction::Read));
	}

	protected function deleteEntity(mixed $id, ?string $class = null): int
	{
		$entity = $this->lookupEntity($id, $class);
		if ($entity) {
			$this->entity_manager->remove($entity);
			$this->entity_manager->flush();
			return 1;
		}
		return 0;
	}
}