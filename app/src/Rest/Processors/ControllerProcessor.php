<?php

namespace EDM\Rest\Processors;

use EDM\Rest\Attributes\Controller;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\Tag;
use OpenApi\Context;
use OpenApi\Generator;

class ControllerProcessor
{
	public function __invoke(Analysis $analysis): void
	{
		/** @var Controller[] $controllers */
		$controllers = $analysis->getAnnotationsOfType(Controller::class);
		/** @var OA\Operation[] $operations */
		$operations = $analysis->getAnnotationsOfType(OA\Operation::class);

		$openapi = $analysis->openapi;

		foreach ($controllers as $controller) {
			$path = $controller->path;
			if (Generator::isDefault($path)) {
				$path = false;
			}
			$responses = $controller->responses;
			if ($path == false && $responses == false) {
				continue;
			}
			$tag = $controller->tag;
			foreach ($operations as $operation) {
				if ($this->contextMatch($operation->_context, $controller->_context) == false) {
					continue;
				}
				// update path
				if ($path) {
					$path = $path . '/' . $operation->path;
					$operation->path = str_replace('//', '/', $path);
				}
				if ($responses) {
					$operation->merge($responses, true);
				}
				if ($tag) {
					if (Generator::isDefault($operation->tags)) {
						$operation->tags = [$tag];
					} else {
						$operation->tags[] = $tag;
					}
				}
			}
			if ($tag) {
				$tag = new Tag($tag, $controller->description);
				if (Generator::isDefault($openapi->tags)) {
					$openapi->tags = [$tag];
				} else {
					$openapi->tags[] = $tag;
				}
			}
		}

		foreach ($controllers as $controller) {
			$this->clearMerged($analysis, $controller);
			$this->clearMerged($analysis, $controller->responses);
		}
	}

	protected function contextMatch(?Context $context1, ?Context $context2): bool
	{
		return $context1 && $context2
			&& $context1->namespace === $context2->namespace
			&& $context1->class == $context2->class;
	}

	protected function clearMerged(Analysis $analysis, $annotations): void
	{
		if (Generator::isDefault($annotations) || !$annotations) {
			return;
		}

		$annotations = is_array($annotations) ? $annotations : [$annotations];

		foreach ($annotations as $annotation) {
			$analysis->annotations->detach($annotation);
		}
	}
}