<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

	#[Route('/', name:'homepage')]
	public function index (PostsRepository $postsRepository) {
		$posts = $postsRepository->findAll();
		return $this->render("home/home.html.twig", [
			"posts" => $posts
		]);
	}
	#[Route('/mailme', name:"mailme")]
	public function mailMe (MailerInterface $mailer, PostsRepository $postsRepository) {
		$email = (new Email())
			->from("snowtricks@gmail.com")
			->to("samuel.brb19@gmail.com")
			->subject("I'm a test")
			->text("I'm really a test")
			->html('<p>See Twig integration for better HTML integration!</p>');
		
		try {
			$mailer->send($email);
			echo "Email sent !";
		} catch (TransportExceptionInterface $e) {
			$error = $e->getMessage();
			echo $e->getMessage();
			dump($e);
		}

		$posts = $postsRepository->findAll();

		return $this->render("home/home.html.twig", [
			"posts" => $posts,
			"error" => (isset($error) ? $error : "")
		]);
	}
}