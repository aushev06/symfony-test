<?php

namespace EDM\Enums;

enum CRUDAction: string
{
	case List   = 'list';
	case Create = 'create';
	case Read   = 'read';
	case Update = 'update';
	case Delete = 'delete';
}