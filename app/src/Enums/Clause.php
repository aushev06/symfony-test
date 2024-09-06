<?php

namespace EDM\Enums;

enum Clause: string
{
	case EQ  = '=';
	case NEQ = '<>';
	case LT  = '<';
	case LTE = '<=';
	case GT  = '>';
	case GTE = '>=';

	case LIKE     = 'LIKE';
	case NOT_LIKE = 'NOT LIKE';

	case CONTAINS = 'CONTAINS';
	case STARTS_WITH = 'STARTS WITH';
	case ENDS_WITH = 'ENDS WITH';
}