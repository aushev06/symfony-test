<?php

namespace EDM\EntityTracking;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use EDM\Access;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use OpenApi\Attributes as OA;

trait EntityTimeTrackedTrait
{
	#[Assert\NotNull]
	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	#[Groups(Access::REGULAR_READ)]
	#[OA\Property(description: 'Время создания', type: 'string', format: 'date-time')]
	protected \DateTime $created_at;

	#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
	#[Groups(Access::REGULAR_READ)]
	#[OA\Property(description: 'Время последнего обновления', type: 'string', format: 'date-time', nullable: true)]
	protected ?\DateTime $updated_at = null;

	public function __construct()
	{
		$this->created_at = new \DateTime();
	}

	public function getCreatedAt(): \DateTime
	{
		return $this->created_at;
	}

	public function getUpdatedAt(): ?\DateTime
	{
		return $this->updated_at;
	}

	public function setUpdatedAt(?\DateTime $updated_at): static
	{
		$this->updated_at = $updated_at;
		return $this;
	}

	public function triggerUpdate(): static
	{
		$this->updated_at = new \DateTime();
		return $this;
	}
}