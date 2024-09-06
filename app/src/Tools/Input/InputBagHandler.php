<?php

namespace EDM\Tools\Input;

use Symfony\Component\HttpFoundation\ParameterBag;

class InputBagHandler
{
	use InputReadTrait;

	private ParameterBag $bag;

	public function __construct(ParameterBag $bag)
	{
		$this->bag = $bag;
	}

	public function getBag(): ParameterBag
	{
		return $this->bag;
	}

	public function setBag(ParameterBag $bag): static
	{
		$this->bag = $bag;
		return $this;
	}

	public function get(string $key, $default = null)
	{
		return $this->bag->get($key, $default);
	}
}