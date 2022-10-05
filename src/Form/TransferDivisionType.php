<?php

namespace App\Form;

use App\Entity\SettlementMember;
use App\Model\Form\TransferDivisionData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferDivisionType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('memberId', EntityType::class, [
				'class' => SettlementMember::class,
				'property_path' => 'member'
			])
			->add('amount', NumberType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => TransferDivisionData::class,
			'allow_extra_fields' => true
		]);
	}
}
