<?php

namespace EDM\Exceptions;

class NotFoundException extends InvalidInputException
{
	public function __construct(?string $name = null, mixed $id = null, ?int $code = null, ?\Throwable $previous = null)
	{
		$message = 'Target ';
		if ($name) {
			$message .= $name . ' ';
		}
		if ($id) {
			$message .= 'with ID #' . $id . ' ';
		}
		$message .= 'not found.';
		parent::__construct($message, $code, $previous);
	}
}