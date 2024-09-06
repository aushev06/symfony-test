<?php

namespace EDM\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use EDM\Access;

use Symfony\Component\Serializer\Attribute\Groups;

use Symfony\Component\Validator\Constraints as Assert;

use OpenApi\Attributes as OA;

#[ORM\Entity]
#[ORM\Table(name: 'limit_applications')]
#[OA\Schema(description: 'Заявка на увеличение лимита по договору поставки товара')]
class LimitApplication extends AbstractApplication
{
	#[Assert\NotNull]
	#[ORM\ManyToOne(targetEntity: Supplier::class, cascade: ['persist'], inversedBy: 'limit_applications')]
	#[ORM\JoinColumn(name: 'supplier_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
	protected ?Supplier $supplier = null;

	#[ORM\Column(name: '`limit`', type: Types::INTEGER, options: ['unsigned' => true])]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Сумма требуемого лимита по договору', type: 'integer', example: 100000)]
	private int $limit = 0;

	#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Сумма планируемых покупок', type: 'integer', example: 200000)]
	private int $plan = 0;

	#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'На какую сумму покупатель хочет заказывать товара', type: 'integer', example: 150000)]
	private int $potential = 0;

	#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Отсрочка в днях', type: 'integer', example: 7)]
	private int $delay = 0;

	public function getSupplier(): ?Supplier
	{
		return $this->supplier;
	}

	public function setSupplier(?Supplier $supplier): static
	{
		$this->supplier = $supplier;
		return $this;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}

	public function setLimit(int $limit): static
	{
		$this->limit = max(0, $limit);
		return $this;
	}

	public function getPlan(): int
	{
		return $this->plan;
	}

	public function setPlan(int $plan): static
	{
		$this->plan = max(0, $plan);
		return $this;
	}

	public function getPotential(): int
	{
		return $this->potential;
	}

	public function setPotential(int $potential): static
	{
		$this->potential = max(0, $potential);
		return $this;
	}

	public function getDelay(): int
	{
		return $this->delay;
	}

	public function setDelay(int $delay): static
	{
		$this->delay = max(0, $delay);
		return $this;
	}
}