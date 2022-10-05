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
		$data = [
			...$request->query->all(),
			...$request->request->all(),
			...$request->files->all()
		];

		$form = $this->formFactory->create($class, $default);
		$form->submit($data);
		if (!$form->isValid()) {
			throw new FormValidationException($form);
		}
		return $form;
	}
}
