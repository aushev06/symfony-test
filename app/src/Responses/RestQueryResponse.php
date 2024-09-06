<?php

namespace EDM\Responses;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;

use EDM\Enums\Clause;

class RestQueryResponse extends AbstractRestResponse
{
	public const DEFAULT_ALIAS = 'e';

	private string $default_alias = self::DEFAULT_ALIAS;

	public function __construct(private QueryBuilder $query_builder, int $status = 200, array $headers = [])
	{
		parent::__construct($status, $headers);
	}

	public function getQueryBuilder(): QueryBuilder
	{
		return $this->query_builder;
	}

	public function getDefaultAlias(): string
	{
		return $this->default_alias;
	}

	public function setDefaultAlias(string $default_alias): static
	{
		$this->default_alias = $default_alias;
		return $this;
	}

	protected function getData(): mixed
	{
		$this->query_builder->setMaxResults(100);

		$q = $this->query_builder->getQuery();

		return $q->execute();
	}

	private function checkKey(string|array $key): array
	{
		if ($key == false) {
			throw new \Exception('Empty or invalid key provided.');
		}
		if (is_string($key)) {
			$key = explode('.', $key, 2);
		}
		if (count($key) == 1) {
			return [$this->default_alias, $key[0]];
		}
		return $key;
	}

	public function filterBy(string|array $key, mixed $value, Clause $clause = Clause::EQ, ?string $parameter_key = null): static
	{
		[$alias, $key] = $this->checkKey($key);
		if ($parameter_key === null) {
			$parameter_key = str_replace('.', '_', $key);
		}
		switch ($clause) {
			case Clause::CONTAINS:
				$value = '%' . $value . '%';
				$operator = Clause::LIKE->value;
				break;

			case Clause::STARTS_WITH:
				$value = '%' . $value;
				$operator = Clause::LIKE->value;
				break;

			case Clause::ENDS_WITH:
				$value = $value . '%';
				$operator = Clause::LIKE->value;
				break;

			default:
				$operator = $clause->value;
		}
		$this->query_builder
			->andWhere(new Comparison($alias . '.' . $key, $operator, ':' . $parameter_key))
			->setParameter($parameter_key, $value);
		return $this;
	}

	public function join(string|array $key, string $alias, bool $left = false): static
	{
		[$key_alias, $key] = $this->checkKey($key);
		if ($left) {
			$this->query_builder->leftJoin($key_alias . '.' . $key, $alias);
		} else {
			$this->query_builder->innerJoin($key_alias . '.' . $key, $alias);
		}
		return $this;
	}
}