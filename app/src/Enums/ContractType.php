<?php

namespace EDM\Enums;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Форма договора')]
enum ContractType: string
{
	case Buyer               = 'buyer';
	case Supplier            = 'supplier';
	case SupplierWithRemarks = 'supplier_remarks';

	public function caption(): string
	{
		return match ($this) {
			self::Buyer               => 'Договор по форме покупателя',
			self::Supplier            => 'Договор по форме поставщика',
			self::SupplierWithRemarks => 'Договор по форме поставщика с протоколом разногласий'
		};
	}
}