<?php

namespace EDM;

use EDM\Enums\CRUDAction;
use EDM\Enums\UserType;

class Access
{
	public const SEPARATOR = ':';

	public const REGULAR = UserType::Regular->value;
	public const MANAGER = UserType::Manager->value;

	public const CREATE = CRUDAction::Create->value;
	public const READ   = CRUDAction::Read->value;
	public const UPDATE = CRUDAction::Update->value;

	public const REGULAR_CREATE = self::REGULAR . self::SEPARATOR . self::CREATE;
	public const REGULAR_READ   = self::REGULAR . self::SEPARATOR . self::READ;
	public const REGULAR_UPDATE = self::REGULAR . self::SEPARATOR . self::UPDATE;

	public const MANAGER_CREATE = self::MANAGER . self::SEPARATOR . self::CREATE;
	public const MANAGER_READ   = self::MANAGER . self::SEPARATOR . self::READ;
	public const MANAGER_UPDATE = self::MANAGER . self::SEPARATOR . self::UPDATE;

	public const EMBEDDED = 'embedded';
	public const EXTRA    = 'extra';

	public static function embedded(string $name): string
	{
		return self::EMBEDDED . self::SEPARATOR . $name;
	}

	public static function extra(string $name): string
	{
		return self::EXTRA . self::SEPARATOR . $name;
	}
}