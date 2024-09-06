<?php

namespace EDM\Enums;

enum UserType: string
{
	case Regular = 'regular'; // registered user
	case Manager = 'manager'; // manager

	public function caption(): string
	{
		return match ($this) {
			self::Regular => 'registered user',
			self::Manager => 'manager'
		};
	}
}
