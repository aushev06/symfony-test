<?php

namespace EDM\Tools\Input;

trait InputReadTrait
{
	private array $cache = [];

	private function internalReadArray(
		string $key, bool $string_packed,
	  bool $filter = true, bool $unique = false,
		?callable $map_callback = null, ?callable $filter_callback = null, bool $strict = false
	): array {
		$result = $this->get($key);
		if ($filter) {
			if ($result == false) {
				return [];
			}
		} else if ($result === null) {
			return [];
		}
		if ($string_packed && is_string($result)) {
			$result = explode(',', $result);
		}
		if (is_array($result) == false) {
			if ($strict) {
				return [];
			}
			$result = (array) $result;
		}
		if ($map_callback) {
			$result = array_map($map_callback, $result);
		}
		if ($filter) {
			$result = $filter_callback ? array_filter($result, $filter_callback) : array_filter($result);
		}
		if ($unique) {
			$result = array_unique($result, SORT_REGULAR);
		}
		return array_values($result);
	}

	public function readArray(string $key, bool $filter = true, bool $unique = false): array
	{
		return $this->internalReadArray($key, false, $filter, $unique);
	}

	private function checkJSONArray(string $value, $default = null)
	{
		$value = json_decode($value, true);
		return is_array($value) ? $value : $default;
	}

	public function readMultiArray(string $key, bool $filter = true, bool $unique = false, bool $check_json = false): array
	{
		return $this->internalReadArray($key, false, $filter, $unique, fn($value) => is_array($value) ? $value : ($check_json ? $this->checkJSONArray($value, []) : []));
	}

	public function readStringArray(string $key, bool $trim = true, bool $filter = true, bool $unique = false): array
	{
		return $this->internalReadArray($key, true, $filter, $unique, $trim ? 'trim' : null);
	}

	public function readIntArray(string $key, bool $filter = true, bool $unique = false): array
	{
		return $this->internalReadArray($key, true, $filter, $unique, 'intval');
	}

	public function readFloatArray(string $key, bool $filter = true, bool $unique = false): array
	{
		return $this->internalReadArray($key, true, $filter, $unique, 'floatval');
	}

	public function readBoolean(string $key, ?bool $default = null): ?bool
	{
		$result = $this->get($key, $default);
		return $result === $default ? $result : filter_var($result, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE);
	}

	public function readTrimmed(string $key, ?string $default = null): ?string
	{
		$result = $this->get($key, $default);
		return $result === $default ? $result : trim($result);
	}

	public function readAssociativeArray(string $key, ?array $default = null): ?array
	{
		$value = $this->get($key, $default);
		return is_array($value) ? $value : null;
	}

	public function readFlags(string $key): Flags
	{
		return new Flags($this->readStringArray($key, true, true, true));
	}

	public function readIntBounded(string $key, int $minimum = 0, ?int $maximum = null, ?int $default = null): int
	{
		$result = intval($this->get($key, $default === null ? $minimum : $default));
		if ($maximum !== null) {
			$result = min($maximum, $result);
		}
		return max($minimum, $result);
	}

	public function readDateTime(string $key, ?\DateTime $default = null): ?\DateTime
	{
		$value = $this->get($key);
		if ($value && is_string($value) && ($value = strtotime($value))) {
			return \DateTime::createFromFormat('U', $value);
		}
		return $default;
	}
}