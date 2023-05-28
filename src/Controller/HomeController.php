<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

	#[Route('/', name:'homepage')]
	public function index (PostsRepository $postsRepository) {

		//on regarde si l'utilisateur est un admin.
		//Pour les auteurs il faudraqu'ils cliquent sur le post pour afficher en détail afin de pouvoir le modifier / le supprimer
		$isAdmin = ($this->getUser() && in_array("ROLE_ADMIN", $this->getUser()->getRoles()));

		$posts = $postsRepository->findAll();
		return $this->render("home/home.html.twig", [
			"posts" => $posts,
			"isAdmin" => $isAdmin
		]);
	}

}