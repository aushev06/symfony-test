<?php

namespace EDM\Exceptions;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationFailedException extends InvalidInputException
{
	private ConstraintViolationListInterface $violations;

	public function __construct(ConstraintViolationListInterface $violations, ?string $message = null, ?int $code = null, ?\Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->violations = $violations;
	}

	public function getViolations(): ConstraintViolationListInterface
	{
		return $this->violations;
	}

	public function normalize(array $context = []): array
	{
		$result = parent::normalize($context);
		$violations = [];
		foreach ($this->violations as $violation) {
			/** @var ConstraintViolationInterface $violation */
			$violations[] = [
				'path' => $violation->getPropertyPath(),
				'message' => $violation->getMessage(),
				'value' => $violation->getRoot()
			];
		}
		$result['violations'] = $violations;
		return $result;
	}
}