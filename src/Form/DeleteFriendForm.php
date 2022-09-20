<?php

namespace App\Form;

use App\Entity\User;
use App\Model\Form\DeleteFriendData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteFriendForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('userId', EntityType::class, [
				'class' => User::class,
				'property_path' => 'user',
			]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'csrf_protection' => false,
			'data_class' => DeleteFriendData::class
		]);
	}

}
