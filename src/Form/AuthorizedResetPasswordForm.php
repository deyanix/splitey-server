<?php

namespace App\Form;

use App\Model\Form\AuthorizedResetPasswordData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorizedResetPasswordForm  extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('token', TextType::class)
			->add('password', TextType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'csrf_protection' => false,
			'data_class' => AuthorizedResetPasswordData::class
		]);
	}

}
