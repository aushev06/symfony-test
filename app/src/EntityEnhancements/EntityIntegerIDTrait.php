<?php

namespace EDM\EntityEnhancements;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use EDM\Access;

use OpenApi\Attributes as OA;

use Symfony\Component\Serializer\Attribute\Groups;

trait EntityIntegerIDTrait
{
	#[ORM\Id]
	#[ORM\Column(type: Types::INTEGER)]
	#[ORM\GeneratedValue]
	#[Groups(Access::READ)]
	#[OA\Property(description: 'Уникальный идентификатор', example: '123')]
	protected ?int $id = null;

	public function getID(): ?int
	{
		return $this->id;
	}
}