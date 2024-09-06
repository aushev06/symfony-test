<?php

namespace SPR\Responses;

class EmbeddedType
{
	private bool $active;
	private string $group;
	private string $relation;

	public function __construct(string $group, string $relation, bool $active = false)
	{
		$this->active = $active;
		$this->group = $group;
		$this->relation = $relation;
	}

	public function isActive(): bool
	{
		return $this->active;
	}

	public function setActive(bool $active): self
	{
		$this->active = $active;
		return $this;
	}

	public function activate(): self
	{
		$this->active = true;
		return $this;
	}

	public function deactivate(): self
	{
		$this->active = false;
		return $this;
	}

	public function getGroup(): string
	{
		return $this->group;
	}

	public function setGroup(string $group): self
	{
		$this->group = strtolower(trim($group));
		return $this;
	}

	public function getRelation(): string
	{
		return $this->relation;
	}

	public function setRelation(string $relation): self
	{
		$this->relation = trim($relation);
		return $this;
	}
}