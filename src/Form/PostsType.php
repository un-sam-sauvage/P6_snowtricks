<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name')
			->add('content')
			->add('categories')
			->add('author')
			->add('media', FileType::class, 
				array(
					'label' => 'image',
					'mapped' => false,
					'multiple' => 'multiple',
					'constraints' => [
						new File([
							'maxSize' => '1024k',
							'mimeTypes' => [
								'image/jpeg',
								'image/png',
							],
							'mimeTypesMessage' => 'Please upload a valid PDF document',
						])
					]
				)
			)
			//ca ne me convient pas à voir ce qu'on peut faire de mieux
			->add('video', CollectionType::class, 
				[
					'entry_type' => TextType::class,
					'mapped' => false,
					'allow_add' => true,
					'prototype' => true
				]
			)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Posts::class,
		]);
	}
}
