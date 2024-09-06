<?php

namespace EDM\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class AbstractClientException extends \Exception
{
	protected const STATUS_CODE = Response::HTTP_I_AM_A_TEAPOT;
	protected const MESSAGE = null;

	public function __construct(?string $message = null, ?int $code = null, ?\Throwable $previous = null)
	{
		$code = $code ?? static::STATUS_CODE;
		parent::__construct($message ?? (static::MESSAGE ?? (Response::$statusTexts[$code] ?? '')), $code, $previous);
	}
}