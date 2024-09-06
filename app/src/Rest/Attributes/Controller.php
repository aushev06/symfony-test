<?php

namespace EDM\Rest\Attributes;

use OpenApi\Attributes as OA;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Controller extends OA\Attachable
{
	/**
	 * @param OA\Response[]|null       $responses
	 * @param string[]|null            $tags
	 * @param array<string,mixed>|null $x
	 * @param OA\Attachable[]|null     $attachables
	 */
	public function __construct(
		public readonly ?string $path = null,
		public readonly ?array $responses = null,
		public readonly ?string $tag = null,
		public readonly ?string $description = null,
		// annotation
		?array $x = null,
		?array $attachables = null
	) {
		parent::__construct([
			'x'      => $x ?? Generator::UNDEFINED,
			'value'  => $this->combine($responses, $attachables),
		]);
	}
}