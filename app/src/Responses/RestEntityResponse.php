<?php

namespace EDM\Responses;

use Symfony\Component\HttpFoundation\Request;

class RestEntityResponse extends AbstractRestResponse
{
	public function __construct(private ?object $entity, int $status = 200, array $headers = [])
	{
		parent::__construct($status, $headers);
		$this->headers->set('Content-Type', 'application/json');
	}

	public function getEntity(): ?object
	{
		return $this->entity;
	}

	public function setEntity(?object $entity): self
	{
		$this->entity = $entity;
		return $this;
	}

	protected function getData(): ?object
	{
		return $this->entity;
	}

	protected function getPreparedData(Request $request): array
	{
		return [
			'item' => $this->normalize()
		];
	}
}