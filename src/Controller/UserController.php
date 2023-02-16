<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController {
	#[Route("/create-user", name:"create-user")]
	public function createUser(ManagerRegistry $doctrine, ValidatorInterface $validator): Response {
		//get the db object
		$db = $doctrine->getManager();

		//create a new user object
		$user = new Users();

		
		$user->setUsername("Sam");
		
		//handle all errors that can be thrown from above
		$errors = $validator->validate($user);
		if(count($errors) > 0) {
			return new Response((string) $errors, 400);
		}

		//prepare the request maybe it will be execute maybe not
		$db->persist($user);

		//executes the queries
		$db->flush();

		return new Response("user successfully created with id : ". $user->getId());
	}

	#[Route("/profile-page/{id}", name:"profile-page")]
	public function getUser (ManagerRegistry $doctrine, int $id) {
		$user = $doctrine->getRepository(Users::class)->find($id);

		if (!$user) {
			return new Response("user not found :", 404);
		}
	}

}