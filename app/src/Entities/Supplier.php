<?php

namespace EDM\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use EDM\Access;

use OpenApi\Attributes as OA;

#[ORM\Entity]
#[ORM\Table(name: 'suppliers')]
#[OA\Schema(description: 'Supplier')]
class Supplier extends AbstractEntity
{
	#[Assert\NotBlank(message: 'Name can\'t be blank.')]
	#[ORM\Column(type: 'string', length: 512, unique: true)]
	#[Groups(Access::REGULAR)]
	#[OA\Property(description: 'Name', type: 'string', example: 'Sweet Life')]
	private string $name = '';

	// TODO: 1С integration goes here, maybe better as a separate entity?

	#[ORM\OneToMany(targetEntity: ContractApplication::class, mappedBy: 'supplier', cascade: ['persist', 'remove'])]
	private Collection $contract_applications;

	#[ORM\OneToMany(targetEntity: LimitApplication::class, mappedBy: 'supplier', cascade: ['persist', 'remove'])]
	private Collection $limit_applications;

	public function __construct()
	{
		parent::__construct();
		$this->contract_applications = new ArrayCollection();
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;
		return $this;
	}

	public function getContractApplications(): Collection
	{
		return $this->contract_applications;
	}

	public function setContractApplications(Collection $contract_applications): static
	{
		$this->contract_applications = $contract_applications;
		return $this;
	}

	public function getLimitApplications(): Collection
	{
		return $this->limit_applications;
	}

	public function setLimitApplications(Collection $limit_applications): static
	{
		$this->limit_applications = $limit_applications;
		return $this;
	}
}