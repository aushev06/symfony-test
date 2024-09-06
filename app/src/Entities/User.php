<?php

namespace EDM\Entities;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use EDM\EntityEnhancements\EntityIntegerIDInterface;
use EDM\EntityEnhancements\EntityIntegerIDTrait;

use EDM\EntityTracking\EntityTimeTrackedInterface;
use EDM\EntityTracking\EntityTimeTrackedTrait;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements EntityIntegerIDInterface, EntityTimeTrackedInterface, UserInterface
{
	use EntityIntegerIDTrait;
	use EntityTimeTrackedTrait;

	#[Assert\NotNull]
	#[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'], inversedBy: 'users')]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
	protected ?Project $project = null;

	#[ORM\Column(type: Types::STRING, length: 255, unique: true)]
	private ?string $login = null;

	#[ORM\Column(type: Types::JSON)]
	private array $roles = [];

	public function getProject(): ?Project
	{
		return $this->project;
	}

	public function setProject(?Project $project): static
	{
		$this->project = $project;
		return $this;
	}

	public function getLogin(): ?string
	{
		return $this->login;
	}

	public function setLogin(?string $login): static
	{
		$this->login = $login;
		return $this;
	}

	public function getUserIdentifier(): string
	{
		return (string) $this->getLogin();
	}

	public function getRoles(): array
	{
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = 'ROLE_USER';
		return array_unique($roles);
	}

	public function setRoles(array $roles): self
	{
		$this->roles = $roles;
		return $this;
	}

	public function eraseCredentials(): void
	{

	}
}