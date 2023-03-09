<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController {

	#[Route('/', name:'homepage')]
	public function index (AuthenticationUtils $authenticationUtils, PostsRepository $postsRepository) {
		//Pour utiliser le $_SESSION il faudra passer par le $request de symfony cf: doc
		$currentUsername = $authenticationUtils->getLastUsername();
		$posts = $postsRepository->findAll();
		return $this->render("home/home.html.twig", [
			"username" => $currentUsername,
			"posts" => $posts
		]);
	}
}