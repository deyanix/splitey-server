<?php

namespace App\Form;

use App\Entity\ExternalFriend;
use App\Entity\User;
use App\Model\Form\SettlementMemberData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettlementMemberType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('userId', EntityType::class, [
				'class' => User::class,
				'property_path' => 'user',
				'required' => false
			])
			->add('externalFriendId', EntityType::class, [
				'class' => ExternalFriend::class,
				'property_path' => 'externalFriend',
				'required' => false
			]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => SettlementMemberData::class,
			'allow_extra_fields' => true
		]);
	}
}
