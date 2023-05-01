<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController {

	#[Route("/profile/{id}", name:"profile")]
	public function index (User $user, Request $request) {

		if ($this->getUser() != null) {
			if ($this->getUser()->getUserIdentifier() == $user->getUsername()) {
				return $this->render("profile/index.html.twig", [
					"test" => $user->getUsername()
				]);
			} else {
				return $this->render("profile/error.html.twig", [
					"message" => "you cannot acess other people's profile"
				]);
			}
		} else {
			return $this->render("profile/error.html.twig", [
				"message" => "Please log in to acess profile"
			]);
		}
	}

	// #[Route("/profile/{id}/edit", name:"edit-profile")]
	// public function editProfile() {
		
	// }
}