<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute]
class ReCaptcha extends Constraint
{
	public $message = 'Captcha response is incorrect';
	public $mode = 'strict';
}


