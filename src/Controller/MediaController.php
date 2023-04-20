<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/media')]
class MediaController extends AbstractController
{
	#[Route('/', name: 'app_media_index', methods: ['GET'])]
	public function index(MediaRepository $mediaRepository): Response
	{
		return $this->render('media/index.html.twig', [
			'media' => $mediaRepository->findAll(),
		]);
	}

	#[Route('/new', name: 'app_media_new', methods: ['GET', 'POST'])]
	public function new(Request $request, MediaRepository $mediaRepository, SluggerInterface $slugger): Response
	{
		$medium = new Media();
		$form = $this->createForm(MediaType::class, $medium);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var UploadedFile $mediaFile */
			$mediaFile = $form->get('path')->getData();

			if ($mediaFile) {
				$originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
				$safeFilename = $slugger->slug($originalFilename);
				$newFilename = $safeFilename .'-'. uniqid() .'.'. $mediaFile->guessExtension();

				try {
					$mediaFile->move(
						//TODO prévoir un directory pour les images et un pour les vidéos
						$this->getParameter('pictures_directory'),
						$newFilename
					);
				} catch	(FileException $e) {
					echo "C'est la merde avec le fichier : ". $e->getMessage();
				}

				$medium->setPath($newFilename);
			}

			$mediaRepository->save($medium, true);

			return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('media/new.html.twig', [
			'medium' => $medium,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'app_media_show', methods: ['GET'])]
	public function show(Media $medium): Response
	{
		return $this->render('media/show.html.twig', [
			'medium' => $medium,
		]);
	}

	#[Route('/{id}/edit', name: 'app_media_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Media $medium, MediaRepository $mediaRepository): Response
	{
		$form = $this->createForm(MediaType::class, $medium);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$mediaRepository->save($medium, true);

			return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('media/edit.html.twig', [
			'medium' => $medium,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'app_media_delete', methods: ['POST'])]
	public function delete(Request $request, Media $medium, MediaRepository $mediaRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$medium->getId(), $request->request->get('_token'))) {
			$mediaRepository->remove($medium, true);
		}

		return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
	}
}
