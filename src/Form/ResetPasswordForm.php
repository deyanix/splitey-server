<?php

namespace App\Form;

use App\Model\Form\ResetPasswordData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('email', EmailType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'csrf_protection' => false,
			'data_class' => ResetPasswordData::class
		]);
	}
}
