<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\{IntegerType, SubmitType, TextType};
use Symfony\Component\Form\{AbstractType, FormBuilderInterface};
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddProductType extends AbstractType
{
	final public function buildForm(FormBuilderInterface $builder, array $_): void
	{
		$builder
			->add("name", TextType::class)
			->add("description", TextType::class)
			->add("price", TextType::class)
			->add("stock", IntegerType::class)
			->add("submit", SubmitType::class, [
				"label" => "Add Product",
			]);
	}

	final public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => AddProductType::class,
		]);
	}
}
