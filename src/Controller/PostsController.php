<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Media;
use App\Form\CommentsType;
use App\Form\PostsType;
use App\Repository\CommentsRepository;
use App\Repository\MediaRepository;
use App\Repository\PostsRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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
	public function new(Request $request, PostsRepository $postsRepository, FileUploader $fileUploader, MediaRepository $mediaRepository): Response
	{
		$post = new Posts();
		$form = $this->createForm(PostsType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$postsRepository->save($post, true);

			/** @var UploadedFile $imageFile */
			$imageFile = $form->get('media')->getData();
			foreach ($imageFile as $image) {
				if ($image) {
					$imageFileName = $fileUploader->upload($image);
					$media = new Media();
					$media->setPath($imageFileName);
					$media->setPost($post);
					$media->setIsVideo(false);
					$mediaRepository->save($media, true);
				}
			}

			$videos = $form->get('videos')->getData();
			foreach ($videos as $videoLink) {
				if ($videoLink) {
					$videoLink = str_replace("watch?v=", "embed/", $videoLink);
					$video = new Media();
					$video->setPath($videoLink);
					$video->setPost($post);
					$video->setIsVideo(true);
					$mediaRepository->save($video, true);
				}
			}

			return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('posts/new.html.twig', [
			'post' => $post,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'app_posts_show', methods: ['GET'])]
	public function show(Posts $post, CommentsRepository $commentsRepository, Request $request): Response
	{
		$isAuthor = ($this->getUser() == $post->getAuthor() ? true : false);
		$isAdmin = ($this->getUser() && in_array("ROLE_ADMIN", $this->getUser()->getRoles()));
		
		$form = $this->createForm(CommentsType::class, null, [
			'action' => $this->generateUrl("app_comments_new", ["id" => $post->getId()]) ,
			'method' => 'POST'
		]);
		$page = (int)$request->query->get("page", 1);
		$comments = $commentsRepository->getPaginatedComments($page, 1, $post);

		return $this->render('posts/show.html.twig', [
			'post' => $post,
			'form' => $form,
			'isAuthor' => $isAuthor,
			"isAdmin" => $isAdmin,
			"comments" => $comments
		]);
	}

	#[Route('/getComment/{id}', name: 'app_get_comments', methods: ['POST'])]
	public function getComment (Posts $post, CommentsRepository $commentsRepository, Request $request): Response {
		$params = json_decode($request->getContent(), true);
		$comments = $commentsRepository->getPaginatedComments($params["page"], 1, $post);
		$commentReturn = array();
		foreach($comments as $comment) {
			$commentReturn[] = [
				"ID" => $comment->getId(),
				"title" => $comment->getTitle(),
				"content" => $comment->getContent(),
				"author" => [
					"username" => $comment->getAuthor()->getUsername(),
					"imgPath" => $comment->getAuthor()->getImgPath()
				],
				"createdAt" => $comment->getCreatedAt()
				// "createdAt" => date("Y-m-d", strtotime($comment->getCreatedAt()))
			];
		}
		return new JsonResponse(
			array(
				"comments" => $commentReturn,
				"msg" => "everything went well"
			)
		);
	}

	#[Route('/{id}/edit', name: 'app_posts_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Posts $post, PostsRepository $postsRepository, FileUploader $fileUploader, MediaRepository $mediaRepository): Response
	{
		$form = $this->createForm(PostsType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$postsRepository->save($post, true);

			/** @var UploadedFile $imageFile */
			$imageFile = $form->get('media')->getData();
			foreach ($imageFile as $image) {
				if ($image) {
					$imageFileName = $fileUploader->upload($image);
					$media = new Media();
					$media->setPath($imageFileName);
					$media->setPost($post);
					$media->setIsVideo(false);
					$mediaRepository->save($media, true);
				}
			}
   
			$videos = $form->get('video')->getData();
			foreach ($videos as $video) {
				if ($video) {
					$videoLink = new Media();
					$video = str_replace("watch?v=", "embed/", $video);
					$videoLink->setPath($video);
					$videoLink->setPost($post);
					$videoLink->setIsVideo(true);
					$mediaRepository->save($videoLink, true);
				}
			}
			return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('posts/edit.html.twig', [
			'post' => $post,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'app_posts_delete', methods: ['POST'])]
	public function delete(Request $request, Posts $post, PostsRepository $postsRepository): Response
	{
		$params = json_decode($request->getContent(), true);
		if ($this->isCsrfTokenValid('delete'.$post->getId(), $params["_token"])) {
			$postsRepository->remove($post, true);
		} else {
			return new JsonResponse(
				array(
					"result" => "fail",
					"msg" => "InvalidToken"
				)
			);
		}

		return new JsonResponse(
			array(
				"result" => "success",
				"msg" => "everything went well"
			)
		);
	}
}
