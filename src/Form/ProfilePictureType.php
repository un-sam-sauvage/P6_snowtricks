<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilePictureType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('imgPath', FileType::class,
				array(
					'label' => 'profile picture',
					'required' => true,
					'constraints' => [
						new File([
							'maxSize' => '1024k',
							'mimeTypes' => [
								'images/jpeg',
								'image/png',
							],
							'mimeTypesMessage' => 'Please upload a valid img document',
						])
					]
				)
			)
		;
	}

	// public function configureOptions(OptionsResolver $resolver): void
	// {
	// 	$resolver->setDefaults([
	// 		'data_class' => User::class,
	// 	]);
	// }
}
