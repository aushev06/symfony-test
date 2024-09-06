<?php

namespace EDM\EventListeners;

use EDM\Normalizers\ExceptionNormalizer;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: KernelEvents::EXCEPTION, priority: 120)]
readonly class ExceptionListener
{
	public function __construct(
		private SerializerInterface $serializer,
		#[Autowire('%kernel.debug%')] private bool $debug)
	{	}

	public function __invoke(ExceptionEvent $event): void
	{
		$exception = $event->getThrowable();

		$response = new JsonResponse();
		$context = [];
		if ($this->debug) {
			$context[ExceptionNormalizer::NORMALIZE_INCLUDE_TRACE] = true;
		}
		$response->setData($this->serializer->normalize($exception, null, $context));

		if ($exception instanceof HttpExceptionInterface) {
			$response->setStatusCode($exception->getStatusCode());
			$response->headers->replace($exception->getHeaders());
		} else {
			$response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		// sends the modified response object to the event
		$event->setResponse($response);
	}
}