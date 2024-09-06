<?php

namespace EDM\Tools\Input;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class InputRequestHandler
{
	use InputReadTrait;

	private Request $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function getRequest(): Request
	{
		return $this->request;
	}

	public function setRequest(Request $request): static
	{
		$this->request = $request;
		return $this;
	}

	public function has(string $key): bool
	{
		return
			$this->request->attributes->has($key) ||
			$this->request->query->has($key) ||
			$this->request->request->has($key);
	}

	public function get(string $key, $default = null)
	{
		return $this->request->get($key, $default);
	}

	public function getInt(string $key, ?int $default = null): ?int
	{
		$result = $this->get($key, $default);
		return $result === $default ? $result : (int) $result;
	}

	public function getFile(string $key): ?UploadedFile
	{
		$result = $this->request->files->get($key);
		return is_array($result) ? ($result[0] ?? null) : $result;
	}

	/**
	 * @return UploadedFile[]|null
	 */
	public function getFiles(string $key): ?array
	{
		$result = $this->request->files->get($key);
		return is_null($result) ? null : (is_array($result) ? $result : [$result]);
	}
}