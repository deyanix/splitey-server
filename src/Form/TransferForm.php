<?php

namespace App\Form;

use App\Entity\SettlementMember;
use App\Model\Form\TransferData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', TextType::class)
			->add('payingMemberId', EntityType::class, [
				'class' => SettlementMember::class,
				'property_path' => 'payingMember'
			])
			->add('divisions', CollectionType::class, [
				'entry_type' => TransferDivisionType::class,
				'allow_add' => true,
				'allow_delete' => true
			]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => TransferData::class,
			'csrf_protection' => false,
			'allow_extra_fields' => true
		]);
	}
}
