<?php

namespace EDM\Entities;

use EDM\Enums\ApplicationStatus;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use OpenApi\Attributes as OA;

abstract class AbstractApplication extends AbstractEntity
{
	#[ORM\Column(type: Types::STRING, enumType: ApplicationStatus::class)]
	#[OA\Property]
	public ApplicationStatus $status = ApplicationStatus::New;

	#[Assert\NotBlank(message: 'ИНН не может быть пустым.')]
	#[Assert\Length(min: 10, max: 12)]
	#[Assert\Type(type: 'digit', message: 'Передан неверный ИНН.')]
	#[ORM\Column(type: Types::STRING, length: 12)]
	#[OA\Property(description: 'ИНН организации', type: 'string', example: '')]
	private string $inn = '';

	#[ORM\Column(type: Types::BOOLEAN)]
	#[OA\Property(description: 'Является ли организация ИП', type: 'boolean')]
	private bool $pe = false;

	public function getStatus(): ApplicationStatus
	{
		return $this->status;
	}

	public function setStatus(ApplicationStatus $status): static
	{
		$this->status = $status;
		return $this;
	}

	public function getINN(): string
	{
		return $this->inn;
	}

	public function setINN(string $inn): static
	{
		$this->inn = trim($inn);
		return $this;
	}

	public function isPE(): bool
	{
		return $this->pe;
	}

	public function setPE(bool $pe): static
	{
		$this->pe = $pe;
		return $this;
	}
}