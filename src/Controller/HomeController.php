<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController {

	#[Route('/', name:'homepage')]
	public function index (AuthenticationUtils $authenticationUtils) {
		//Pour utiliser le $_SESSION il faudra passer par le $request de symfony cf: doc
		$currentUsername = $authenticationUtils->getLastUsername();
		return $this->render("home/home.html.twig", [
			"username" => $currentUsername
		]);
	}
}