<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Models\DB;

class HomeController extends AbstractController{

	#[Route('/')]
	public function index () {
	}
}