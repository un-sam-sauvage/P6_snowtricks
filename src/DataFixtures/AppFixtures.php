<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\Posts;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

	public function __construct( private SluggerInterface $slugger, private UserPasswordHasherInterface $passwordHasher)
	{

	}

	public function load(ObjectManager $manager): void
	{
		
		$userAdmin = new User();
		$userAdmin->setUsername("fixturesUser@test.com");
		$userAdmin->setRoles(array("ROLE_ADMIN"));
		$userAdmin->setImgPath("defaultPicture.png");
		$userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, "123456"));
		$manager->persist($userAdmin);

		for ($i = 0; $i < 20; $i++) {

			$post = new Posts();
			$post->setName('post '.$i);

			$postName = $post->getName();
			$slug = $this->slugger->slug($postName)->lower();

			$post->setSlug($slug);
			$post->setContent("Content : ". $i);
			$post->setAuthor($userAdmin);

			$manager->persist($post);

			$comment = new Comments();
			$comment->setTitle("I'm a comment for post ". $i);
			$comment->setContent("I'm the content for comment of post ". $i);
			$comment->setPosts($post);
			$comment->setAuthor($userAdmin);
			$comment->setCreatedAt(new DateTimeImmutable());

			$manager->persist($comment);
		}
		$manager->flush();
	}
}
