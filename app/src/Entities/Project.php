<?php

namespace EDM\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'projects')]
class Project extends AbstractEntity
{
	#[Assert\NotBlank(message: 'Project code can\'t be blank.')]
	#[ORM\Column(type: 'string', length: 255, unique: true)]
	private string $code = '';

	#[Assert\NotBlank(message: 'Project name can\'t be blank.')]
	#[ORM\Column(type: 'string', length: 512)]
	private string $name = '';

	#[ORM\OneToMany(targetEntity: User::class, mappedBy: 'project', cascade: ['persist', 'remove'])]
	private Collection $users;

	public function __construct()
	{
		parent::__construct();
		$this->users = new ArrayCollection();
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function setCode(string $code): static
	{
		$this->code = strtolower(trim($code));
		return $this;
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

	public function getUsers(): Collection
	{
		return $this->users;
	}

	public function setUsers(Collection $users): static
	{
		$this->users = $users;
		return $this;
	}
}