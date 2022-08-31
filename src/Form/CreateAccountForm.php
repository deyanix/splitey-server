<?php

namespace App\Form;

use App\Entity\Settlement;
use App\Entity\User;
use App\Model\CreateAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateAccountForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', TextType::class)
			->add('lastName', TextType::class)
			->add('username', TextType::class)
			->add('email', EmailType::class)
			->add('password', PasswordType::class)
			->add('captcha', TextType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'csrf_protection' => false,
			'data_class' => CreateAccount::class
		]);
	}

}
