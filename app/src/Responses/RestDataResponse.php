<?php

namespace EDM\Responses;

use Symfony\Component\HttpFoundation\Request;

class RestDataResponse extends AbstractRestResponse
{
	public function __construct(private mixed $data, int $status = 200, array $headers = [])
	{
		parent::__construct($status, $headers);
		$this->headers->set('Content-Type', 'application/json');
	}

	public function getData(): mixed
	{
		return $this->data;
	}

	public function setData(mixed $data): static
	{
		$this->data = $data;
		return $this;
	}

	protected function getPreparedData(Request $request): array
	{
		return [
			'result' => $this->normalize()
		];
	}
}