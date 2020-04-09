<?php

declare(strict_types=1);

namespace App\UI\Admin\Login\Form;

use Nette\SmartObject;

class LoginFormDTO
{
	use SmartObject;

	/** @var string */
	public $username;

	/** @var string */
	public $password;
}
