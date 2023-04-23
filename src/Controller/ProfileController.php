<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController {

	#[Route("/profile/{id}", name:"profile")]
	public function index (UserRepository $userRepository) {
		return $this->render("profile/index.html.twig");
	}
}