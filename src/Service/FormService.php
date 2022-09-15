<?php

namespace App\Service;

use App\Exception\FormValidationException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormService {
	private FormFactoryInterface $formFactory;

	public function __construct(FormFactoryInterface $formFactory) {
		$this->formFactory = $formFactory;
	}

	public function handle(Request $request, string $class, mixed $default = null): FormInterface {
		$form = $this->formFactory->create($class, $default);

		$form->submit($request->request->all());
		if (!$form->isValid()) {
			throw new FormValidationException($form);
		}
		return $form;
	}
}
