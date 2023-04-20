<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;

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
					'required' => false,
					'mapped' => false,
					'multiple' => true,
					'constraints' => [
						new All ([
							'constraints' => [
								new File([
									'maxSize' => '1024k',
									'mimeTypes' => [
										'image/jpeg',
										'image/png',
									],
									'mimeTypesMessage' => 'Please upload a valid img document',
								])
							]
						])
					]
				)
			)
			->add(
				'videos',
				CollectionType::class,
				[
					'entry_type' => TextType::class,
					'entry_options' => [
						'attr' => ['class' => 'email-box'],
					],
					'allow_add' => true,
					'mapped' => false,
					'required' => false,
					'prototype' => true,
					'label' => 'video',
					// 'allow_extra_fields' => true
				]
			);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Posts::class
		]);
	}
}
