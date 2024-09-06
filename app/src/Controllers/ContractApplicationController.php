<?php

namespace EDM\Controllers;

use EDM\Entities\ContractApplication;
use EDM\Responses\RestEntityResponse;
use EDM\Responses\RestListResponse;

use Nelmio\ApiDocBundle\Annotation\Model;

use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/contract-applications')]
#[OA\Tag(name: 'Заявки на заключение договора поставки товара')]
class ContractApplicationController extends AbstractCRUDController
{
	public const ENTITY_CLASS = ContractApplication::class;

	#[Route(methods: ['GET'])]
	#[OA\Get(summary: 'Список заявок')]
	#[OA\Response(
		response: 200,
		description: 'Contract applications',
		content: new OA\JsonContent(
			properties: [
				new OA\Property(
					'items',
					type: 'array',
					items: new OA\Items(ref: new Model(type: ContractApplication::class, groups: self::GROUPS_READ, options: []))
				)
			],
			type: 'object'
		)
	)]
	public function listContractApplications(): RestListResponse
	{
		return $this->listEntities();
	}

	#[Route(methods: ['POST'])]
	#[OA\RequestBody(content: new Model(type: ContractApplication::class, groups: self::GROUPS_CREATE))]
	public function createContractApplication(): object
	{
		return $this->createEntity();
	}

	#[Route('/{id}', methods: ['GET'])]
	#[OA\Response(
		response: 200,
		description: 'ContractApplication',
		content: new OA\JsonContent(
			properties: [
				new OA\Property(
					'item',
					ref: new Model(type: ContractApplication::class, groups: self::GROUPS_READ),
					type: 'object'
				)
			],
			type: 'object'
		)
	)]
	public function readContractApplication(string $id): RestEntityResponse
	{
		return $this->readEntity($id);
	}

	#[Route('/{id}', methods: ['PUT'])]
	#[OA\RequestBody(content: new Model(type: ContractApplication::class, groups: self::GROUPS_UPDATE))]
	public function updateContractApplication(string $id): RestEntityResponse
	{
		return $this->updateEntity($id);
	}

	#[Route('/{id}', methods: ['DELETE'])]
	public function deleteContractApplication(string $id): int
	{
		return $this->deleteEntity($id);
	}
}

