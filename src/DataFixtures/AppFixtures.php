<?php

namespace App\DataFixtures;

use App\Entity\Posts;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
	public function __construct(private UserRepository $userRepository, private SluggerInterface $slugger)
	{
		
	}

	public function load(ObjectManager $manager): void
	{
		$user = $this->userRepository->findOneByUsername("todo.brb19@gmail.com");
		for ($i = 0; $i < 20; $i++) {
			$post = new Posts();
			$post->setName('post '.$i);
			$postName = $post->getName();
			$slug = $this->slugger->slug($postName)->lower();
			$post->setSlug($slug);
			$post->setContent("Content : ". $i);
			$post->setAuthor($user);
			$manager->persist($post);
		}
		$manager->flush();
	}
}
