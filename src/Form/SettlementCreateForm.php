<?php

namespace App\Form;

use App\Model\Form\SettlementCreateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettlementCreateForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', TextType::class)
			->add('members', CollectionType::class, [
				'entry_type' => SettlementMemberType::class,
				'allow_add' => true
			]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => SettlementCreateData::class,
			'csrf_protection' => false
		]);
	}
}
