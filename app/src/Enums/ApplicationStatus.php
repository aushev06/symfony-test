<?php

namespace EDM\Enums;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Статус заявки')]
enum ApplicationStatus: string
{
	case New                 = 'new';
	case Send                = 'send';
	case NegotiateOfProtocol = 'negotiate_protocol';
	case Negotiate           = 'negotiate';
	case Approved            = 'approved';
	case ApprovedWithRemarks = 'approved_remarks';
	case Declined            = 'declined';
	case Done                = 'done';

	public function caption(): string
	{
		return match ($this) {
			self::New                 => 'Новая',
			self::Send                => 'Отправлена',
			self::NegotiateOfProtocol => 'Согласование протокола разногласий',
			self::Negotiate           => 'Согласование',
			self::Approved            => 'Согласована с замечаниями',
			self::ApprovedWithRemarks => 'Согласована',
			self::Declined            => 'Отклонена',
			self::Done                => 'Выполнена'
		};
	}
}