<?php

namespace App\Form;

use App\Model\Form\ExternalFriendData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalFriendForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', TextType::class)
			->add('lastName', TextType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'csrf_protection' => false,
			'data_class' => ExternalFriendData::class
		]);
	}
}
