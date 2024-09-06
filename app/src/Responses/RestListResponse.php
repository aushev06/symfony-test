<?php

namespace EDM\Responses;

use Symfony\Component\HttpFoundation\Request;

class RestListResponse extends RestQueryResponse
{
	protected function getPreparedData(Request $request): array
	{
		return [
			'items' => $this->normalize()
		];
	}
}