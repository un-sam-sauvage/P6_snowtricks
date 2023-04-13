<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

	#[Route('/', name:'homepage')]
	public function index (PostsRepository $postsRepository, UserRepository $userRepository) {
		
		$currentUsername = "";
		if ($this->getUser()) {
			$currentUser = $userRepository->find($this->getUser());
			$currentUsername = $currentUser->getUsername();
		}
		//TODO: changer par le currentUser 
		$posts = $postsRepository->findAll();
		return $this->render("home/home.html.twig", [
			"username" => $currentUsername,
			"posts" => $posts
		]);
	}
}