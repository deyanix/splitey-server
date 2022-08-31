<?php

namespace App\Validator;

use App\Service\ReCaptchaService;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ReCaptchaValidator extends ConstraintValidator {
	private ReCaptchaService $reCaptchaService;

	public function __construct(ReCaptchaService $reCaptchaService) {
		$this->reCaptchaService = $reCaptchaService;
	}

	public function validate(mixed $value, Constraint $constraint) {
		if (!$constraint instanceof ReCaptcha) {
			throw new UnexpectedTypeException($constraint, ReCaptcha::class);
		}

		if ($value === null || $value === '') {
			return;
		}

		if (!$this->reCaptchaService->checkCaptcha($value)) {
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
	}
}
