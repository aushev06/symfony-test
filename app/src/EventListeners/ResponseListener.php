<?php

namespace EDM\EventListeners;

use EDM\Responses\RestDataResponse;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: KernelEvents::VIEW, priority: 120)]
readonly class ResponseListener
{
	public function __construct(
		private SerializerInterface $serializer
	)	{	}

	public function onKernelView(ViewEvent $event): void
	{
		$response = new RestDataResponse($event->getControllerResult());
		$response->setSerializer($this->serializer);
		$event->setResponse($response);
	}
}