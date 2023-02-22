<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

	#[Route('/', name:'homepage')]
	public function index () {
		//Pour utiliser le $_SESSION il faudra passer par le $request de symfony cf: doc
		return $this->render("home/home.html.twig");
	}
}