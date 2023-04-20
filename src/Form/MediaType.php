<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MediaType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('path', FileType::class, [
				"label" => 'Media',
				"mapped" => false,
				"required" => false,
				"constraints" => [
					new File([
						"maxSize" => "1024k",
						"mimeTypes" => [
							// //à changer
							"image/png",
						],
						"mimeTypesMessage" => "Please upload a valid document",
					])
				],
			])
			->add('isVideo')
			->add('post')
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Media::class,
		]);
	}
}
