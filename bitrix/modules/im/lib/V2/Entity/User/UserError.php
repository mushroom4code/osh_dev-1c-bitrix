<?php

namespace Bitrix\Im\V2\Entity\User;

use Bitrix\Im\V2\Error;
use Bitrix\Main\Localization\Loc;

class UserError extends Error
{
	public const NOT_FOUND = 'USER_NOT_FOUND';

	protected function loadErrorMessage($code, $replacements): string
	{
		return Loc::getMessage("ERROR_USER_{$code}", $replacements) ?: '';
	}

	protected function loadErrorDescription($code, $replacements): string
	{
		return Loc::getMessage("ERROR_USER_{$code}_DESC", $replacements) ?: '';
	}
}