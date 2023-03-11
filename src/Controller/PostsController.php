<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Media;
use App\Form\PostsType;
use App\Repository\PostsRepository;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/posts')]
class PostsController extends AbstractController
{
	#[Route('/', name: 'app_posts_index', methods: ['GET'])]
	public function index(PostsRepository $postsRepository): Response
	{
		return $this->render('posts/index.html.twig', [
			'posts' => $postsRepository->findAll(),
		]);
	}

	#[Route('/new', name: 'app_posts_new', methods: ['GET', 'POST'])]
	public function new(Request $request, PostsRepository $postsRepository, SluggerInterface $slugger, MediaRepository $mediaRepository): Response
	{
		$post = new Posts();
		$form = $this->createForm(PostsType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var UploadedFile $mediaFile */
			$mediaFile = $form->get('media')->getData();

			if ($mediaFile) {
				$originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
				$safeFilename = $slugger->slug($originalFilename);
				$newFilename = $safeFilename.'-'.uniqid().'.'.$mediaFile->guessExtension();

				try {
					$mediaFile->move(
						//TODO: en fonction de si c'est un vidéo ou une images il ne faudra pas les stocker dans le même dossier cf: arborescence
						//pictures_directory || vidoes_directory
						//TODO régler les problèmes.
						$this->getParameter('pictures_directory'),
						$newFilename
					);
				} catch (FileException $e) {
					echo "c'est la merde pour le fichier : " . $e;
				}
				$media = new Media();
				$media->setPath($newFilename);

				//TODO : checker si c'est un vidéo ou pas est intégrer dans la base en fonction
				$media->setIsVideo(false);

				$mediaRepository->save($media, true);

				$post->addMedium($media);
			}

			$postsRepository->save($post, true);

			return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('posts/new.html.twig', [
			'post' => $post,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'app_posts_show', methods: ['GET'])]
	public function show(Posts $post): Response
	{
		return $this->render('posts/show.html.twig', [
			'post' => $post,
		]);
	}

	#[Route('/{id}/edit', name: 'app_posts_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Posts $post, PostsRepository $postsRepository): Response
	{
		$form = $this->createForm(PostsType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$postsRepository->save($post, true);

			return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('posts/edit.html.twig', [
			'post' => $post,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'app_posts_delete', methods: ['POST'])]
	public function delete(Request $request, Posts $post, PostsRepository $postsRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
			$postsRepository->remove($post, true);
		}

		return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
	}
}
