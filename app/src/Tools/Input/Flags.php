<?php

namespace EDM\Tools\Input;

class Flags
{
	private array $flags = [];

	public function __construct(array $flags)
	{
		foreach ($flags as $flag) {
			$this->add($flag);
		}
	}

	public function has(string $flag): bool
	{
		return $this->flags[trim($flag)] ?? false;
	}

	public function add(string $flag): static
	{
		$this->flags[trim($flag)] = true;
		return $this;
	}

	public function remove(string $flag): static
	{
		unset($this->flags[trim($flag)]);
		return $this;
	}

	public function flags(): array
	{
		return array_keys($this->flags);
	}

	public function isEmpty(): bool
	{
		return empty($this->flags);
	}
}