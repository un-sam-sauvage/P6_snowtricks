<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilePictureType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController {

	#[Route("/profile/{id}", name:"profile")]
	public function index (User $user) {
		//TODO: faire en sorte d'afficher la photo en cours afin de la remplacer et pas deux insert
		if ($this->getUser() != null) {
			if ($this->getUser()->getUserIdentifier() == $user->getUsername()) {
				//Faire un autre formType juste pour les photos de profil.
				$form = $this->createForm(ProfilePictureType::class, null, [
					'action' => $this->generateUrl("app_user_edit", ["id" => $user->getId()]),
					'method' => 'POST'
				]);
				return $this->render("profile/index.html.twig", [
					"username" => $user->getUsername(),
					"form" => $form
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

}