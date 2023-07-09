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
	public function new(Request $request, PostsRepository $postsRepository, FileUploader $fileUploader, MediaRepository $mediaRepository, SluggerInterface $slugger): Response
	{
		if (!$this->getUser()) {
			return $this->render("profile/error.html.twig", [
				"message" => "You must be connected to create a post."
			]);
		}
		$post = new Posts();
		$form = $this->createForm(PostsType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$postName = $post->getName();
			$slug = $slugger->slug($postName)->lower();
			$post->setSlug($slug);
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
			$this->addFlash('notice', 'Your post was succesfully created');
			return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('posts/new.html.twig', [
			'post' => $post,
			'form' => $form,
		]);
	}

	#[Route('/{slug}', name: 'app_posts_show', methods: ['GET'])]
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
	public function getComment (Posts $post, CommentsRepository $commentsRepository, Request $request): JsonResponse {
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
			];
		}
		return new JsonResponse(
			array(
				"comments" => $commentReturn,
				"msg" => "everything went well"
			)
		);
	}

	#[Route('/getPosts', name:"app_get_more_posts", methods: ['POST'])] 
	public function getPosts (PostsRepository $postsRepository, Request $request): JsonResponse {
		$params = json_decode($request->getContent(), true);
		$posts = $postsRepository->getPaginatedPost(2, $params["page"]);
		$postsReturn = [];
		$tokenProvider = $this->container->get('security.csrf.token_manager');
		foreach ($posts as $post) {
			$imgPath = (!empty($post->getFirstPicture()) ? 'uploads/pictures/'.$post->getFirstPicture()->getPath() : 'uploads/pictures/defaultPicture.png');
			$token = $tokenProvider->getToken('delete'.$post->getId())->getValue();
			$postsReturn[] = [
				"id" => $post->getId(),
				"name" => $post->getName(),
				"imgPath" => $imgPath,
				"slug" => $post->getSlug(),
				"token" => $token
			];
		}
		return new JsonResponse(
			array(
				"posts" => $postsReturn,
				"msg" => "everything went well"
			)
		);
	}

	#[Route('/{id}/edit', name: 'app_posts_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Posts $post, PostsRepository $postsRepository, FileUploader $fileUploader, MediaRepository $mediaRepository, SluggerInterface $slugger): Response
	{
		$form = $this->createForm(PostsType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$postName = $post->getName();
			$slug = $slugger->slug($postName)->lower();
			$post->setSlug($slug);
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

			if ($form->get('videos')) {
				$videos = $form->get('videos')->getData();
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
