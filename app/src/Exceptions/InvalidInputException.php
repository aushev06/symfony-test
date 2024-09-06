<?php

namespace EDM\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InvalidInputException extends AbstractClientException
{
	protected const STATUS_CODE = Response::HTTP_BAD_REQUEST;
	protected const MESSAGE = null;
}