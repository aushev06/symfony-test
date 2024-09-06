<?php

namespace EDM\Controllers;

use EDM\Entities\Supplier;
use EDM\Enums\Clause;
use EDM\Responses\RestEntityResponse;
use EDM\Responses\RestListResponse;

use Nelmio\ApiDocBundle\Annotation\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

#[Route('/suppliers')]
#[OA\Tag(name: 'Поставщики')]
class SupplierController extends AbstractCRUDController
{
	public const ENTITY_CLASS = Supplier::class;

	#[Route(methods: ['GET'])]
	#[OA\Get(summary: 'Список поставщиков')]
	#[OA\Parameter(
		name: 'name',
		description: 'Search by name',
		in: 'query',
		schema: new OA\Schema(type: 'string')
	)]
	#[OA\Response(
		response: 200,
		description: 'Suppliers',
		content: new OA\JsonContent(
			properties: [
				new OA\Property(
					'items',
					type: 'array',
					items: new OA\Items(ref: new Model(type: Supplier::class, groups: self::GROUPS_READ, options: []))
				)
			],
			type: 'object'
		)
	)]
	public function listSuppliers(Request $request): RestListResponse
	{
		$list = $this->listEntities();

		if ($name = $request->query->getDigits('name')) {
			$list->filterBy('name', $name, Clause::CONTAINS);
		}

		return $list;
	}

	#[Route(methods: ['POST'])]
	#[OA\RequestBody(content: new Model(type: Supplier::class, groups: self::GROUPS_CREATE))]
	public function createSupplier(): object
	{
		return $this->createEntity();
	}

	#[Route('/{id}', methods: ['GET'])]
	#[OA\Response(
		response: 200,
		description: 'Supplier',
		content: new OA\JsonContent(
			properties: [
				new OA\Property(
					'item',
					ref: new Model(type: Supplier::class, groups: self::GROUPS_READ),
					type: 'object'
				)
			],
			type: 'object'
		)
	)]
	public function readSupplier(string $id): RestEntityResponse
	{
		return $this->readEntity($id);
	}

	#[Route('/{id}', methods: ['PUT'])]
	#[OA\RequestBody(content: new Model(type: Supplier::class, groups: self::GROUPS_UPDATE))]
	public function updateSupplier(string $id): RestEntityResponse
	{
		return $this->updateEntity($id);
	}

	#[Route('/{id}', methods: ['DELETE'])]
	public function deleteSupplier(string $id): int
	{
		return $this->deleteEntity($id);
	}
}

