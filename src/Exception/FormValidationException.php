<?php

namespace App\Exception;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormInterface;
use Throwable;

class FormValidationException extends Exception {
	private FormInterface $form;

	public function __construct(FormInterface $form, $message = "", $code = 0, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
		$this->form = $form;
	}

	public function getForm(): FormInterface {
		return $this->form;
	}

}
