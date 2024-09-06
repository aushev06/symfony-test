<?php

namespace EDM\EntityTracking;

use EDM\Access;
use EDM\Entities\User;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Attribute\Groups;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;

trait EntityUserTrackedTrait
{
	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	protected ?User $created_by = null;

	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	protected ?User $updated_by = null;

	public function getCreatedBy(): ?User
	{
		return $this->created_by;
	}

	#[Groups(Access::MANAGER_READ)]
	#[SerializedName('created_by')]
	#[OA\Property('created_by', description: 'Идентификатор создателя', type: 'integer', example: 123, nullable: true)]
	public function getCreatedByID(): ?int
	{
		return $this->created_by?->getID();
	}

	public function setCreatedBy(?User $created_by): static
	{
		$this->created_by = $created_by;
		return $this;
	}

	public function getUpdatedBy(): ?User
	{
		return $this->updated_by;
	}

	#[Groups(Access::MANAGER_READ)]
	#[SerializedName('updated_by')]
	#[OA\Property('updated_by', description: 'Идентификатор последнего обновившего', type: 'integer', example: 123, nullable: true)]
	public function getUpdatedByID(): ?int
	{
		return $this->updated_by?->getID();
	}

	public function setUpdatedBy(?User $updated_by): static
	{
		$this->updated_by = $updated_by;
		return $this;
	}
}