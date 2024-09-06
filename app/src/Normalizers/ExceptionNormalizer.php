<?php

namespace EDM\Normalizers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExceptionNormalizer implements NormalizerInterface
{
	public const NORMALIZE_INCLUDE_TRACE = 'include_trace';

	public function normalize($object, string $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null
	{
		/** @var \Throwable $object */
		$result = [
			'message' => $object->getMessage(),
			'code' => $object->getCode(),
		];
		if ($context[self::NORMALIZE_INCLUDE_TRACE] ?? false) {
			$result['file'] = $object->getFile();
			$result['line'] = $object->getLine();
			$result['trace'] = $object->getTrace();
		}
		return $result;
	}

	public function supportsNormalization($data, string $format = null, array $context = []): bool
	{
		return $data instanceof \Throwable;
	}

	public function getSupportedTypes(?string $format): array
	{
		return [\Throwable::class => true];
	}
}