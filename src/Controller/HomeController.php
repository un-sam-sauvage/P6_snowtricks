<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

	#[Route('/', name:'homepage')]
	public function index (PostsRepository $postsRepository) {
		$posts = $postsRepository->findAll();
		return $this->render("home/home.html.twig", [
			"posts" => $posts
		]);
	}

}