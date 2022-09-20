<?php

namespace App\Form;

use App\Entity\User;
use App\Model\Form\ExternalFriendData;
use App\Model\Form\FriendInvitationData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FriendInvitationForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('recipientId', EntityType::class, [
				'class' => User::class,
				'property_path' => 'recipient'
			]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'csrf_protection' => false,
			'data_class' => FriendInvitationData::class
		]);
	}
}
