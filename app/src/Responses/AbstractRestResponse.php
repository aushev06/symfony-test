<?php

namespace EDM\Responses;

use EDM\Access;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractRestResponse extends Response
{
	private ?SerializerInterface $serializer = null;

	private array $normalization_groups = [Access::MANAGER_READ];

//	private bool $auto_resolve_embedded = true;
//	/** @var EmbeddedType[] */
//	public array $embedded_types = [];

	private int $encoding_options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

	public function __construct(int $status = 200, array $headers = [])
	{
		parent::__construct('', $status, $headers);
		$this->headers->set('Content-Type', 'application/json');
	}

	public function getSerializer(): ?SerializerInterface
	{
		return $this->serializer;
	}

	public function setSerializer(?SerializerInterface $serializer): self
	{
		$this->serializer = $serializer;
		return $this;
	}

	public function getEncodingOptions(): int
	{
		return $this->encoding_options;
	}

	public function setEncodingOptions(int $encoding_options): self
	{
		$this->encoding_options = $encoding_options;
		return $this;
	}

	public function getNormalizationGroups(): array
	{
		return $this->normalization_groups;
	}

	public function setNormalizationGroups(array $normalization_groups): self
	{
		$this->normalization_groups = $normalization_groups;
		return $this;
	}

	public function isAutoResolveEmbedded(): bool
	{
		return $this->auto_resolve_embedded;
	}

	public function setAutoResolveEmbedded(bool $auto_resolve_embedded): self
	{
		$this->auto_resolve_embedded = $auto_resolve_embedded;
		return $this;
	}

//	/**
//	 * @return EmbeddedType[]
//	 */
//	public function getEmbeddedTypes(): array
//	{
//		return $this->embedded_types;
//	}
//
//	public function setEmbeddedTypes(array $embedded_types): self
//	{
//		$this->embedded_types = [];
//		foreach ($embedded_types as $item) {
//			if ($item instanceof EmbeddedType) {
//				$this->addEmbeddedType($item);
//			}
//		}
//		return $this;
//	}
//
//	public function addEmbeddedType(EmbeddedType $embedded_type): self
//	{
//		$this->embedded_types[$embedded_type->getGroup()] = $embedded_type;
//		return $this;
//	}
//
//	public function embed(string $name, string $relation, ?string $group = null, bool $active = false): self
//	{
//		$this->embedded_types[$name] = new EmbeddedType($group === null ? $name : $group, $relation, $active);
//		return $this;
//	}
//
//	public function switchEmbeddedType(string $key, bool $active): self
//	{
//		if ($embedded = $this->embedded_types[$key] ?? null) {
//			$embedded->setActive($active);
//		}
//		return $this;
//	}

	abstract protected function getData(): mixed;

	protected function getPreparedData(Request $request): mixed
	{
		return $this->normalize();
	}

//	public function resolveEmbedded(Request $request): self
//	{
//		$input = new InputBagHandler($request->query);
//		$embedded = $input->readStringArray('embedded', true, true, true);
//		foreach ($embedded as $key) {
//			$embedded_type = $this->embedded_types[$key] ?? null;
//			if ($embedded_type) {
//				$embedded_type->activate();
//			}
//		}
//	}

	public function prepare(Request $request): static
	{
//		if ($this->auto_resolve_embedded) {
//			$this->resolveEmbedded($request);
//		}
		$this->setContent(json_encode($this->getPreparedData($request), $this->getEncodingOptions()));
		return parent::prepare($request);
	}

	public function normalize(?array $groups = null)
	{
		$data = $this->getData();
		$serializer = $this->getSerializer();
		if ($serializer == null || $data === null || is_scalar($data)) {
			return $data;
		}
		$context = [
			DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s.v'
		];
		$groups = $groups === null ? $this->normalization_groups : $groups;
//		foreach ($this->embedded_types as $embedded_type) {
//			if ($embedded_type->isActive()) {
//				$groups[] = 'embedded.' . $embedded_type->getGroup();
//			}
//		}
		if ($groups) {
			$context[AbstractNormalizer::GROUPS] = $groups;
		}
		return $serializer->normalize($data, null, $context);
	}
}