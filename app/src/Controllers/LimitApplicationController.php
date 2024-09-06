<?php

namespace EDM\Controllers;

use EDM\Entities\LimitApplication;
use EDM\Responses\RestEntityResponse;
use EDM\Responses\RestListResponse;

use Nelmio\ApiDocBundle\Annotation\Model;

use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/limit-applications')]
#[OA\Tag(name: 'Заявки на увеличение лимита по договору поставки товара')]
class LimitApplicationController extends AbstractCRUDController
{
	public const ENTITY_CLASS = LimitApplication::class;

	#[Route(methods: ['GET'])]
	#[OA\Get(summary: 'Список заявок')]
	#[OA\Response(
		response: 200,
		description: 'Limit applications',
		content: new OA\JsonContent(
			properties: [
				new OA\Property(
					'items',
					type: 'array',
					items: new OA\Items(ref: new Model(type: LimitApplication::class, groups: self::GROUPS_READ, options: []))
				)
			],
			type: 'object'
		)
	)]
	public function listLimitApplications(): RestListResponse
	{
		return $this->listEntities();
	}

	#[Route(methods: ['POST'])]
	#[OA\RequestBody(content: new Model(type: LimitApplication::class, groups: self::GROUPS_CREATE))]
	public function createLimitApplication(): object
	{
		return $this->createEntity();
	}

	#[Route('/{id}', methods: ['GET'])]
	#[OA\Response(
		response: 200,
		description: 'LimitApplication',
		content: new OA\JsonContent(
			properties: [
				new OA\Property(
					'item',
					ref: new Model(type: LimitApplication::class, groups: self::GROUPS_READ),
					type: 'object'
				)
			],
			type: 'object'
		)
	)]
	public function readLimitApplication(string $id): RestEntityResponse
	{
		return $this->readEntity($id);
	}

	#[Route('/{id}', methods: ['PUT'])]
	#[OA\RequestBody(content: new Model(type: LimitApplication::class, groups: self::GROUPS_UPDATE))]
	public function updateLimitApplication(string $id): RestEntityResponse
	{
		return $this->updateEntity($id);
	}

	#[Route('/{id}', methods: ['DELETE'])]
	public function deleteLimitApplication(string $id): int
	{
		return $this->deleteEntity($id);
	}
}