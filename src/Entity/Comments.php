<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	#[Assert\NotBlank]
	private ?string $title = null;

	#[ORM\Column(length: 255)]
	#[Assert\NotBlank]
	private ?string $content = null;

	#[ORM\ManyToOne(inversedBy: 'comments')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Posts $posts = null;

	#[ORM\ManyToOne(inversedBy: 'comments')]
	#[ORM\JoinColumn(nullable: false)]
	private ?user $author = null;

	#[ORM\Column (options: ["default" => "CURRENT_TIMESTAMP"])]
	private ?\DateTimeImmutable $created_at;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(string $content): self
	{
		$this->content = $content;

		return $this;
	}

	public function getPosts(): ?Posts
	{
		return $this->posts;
	}

	public function setPosts(?Posts $posts): self
	{
		$this->posts = $posts;

		return $this;
	}

	public function getAuthor(): ?user
	{
		return $this->author;
	}

	public function setAuthor(?user $author): self
	{
		$this->author = $author;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->created_at;
	}

	public function setCreatedAt(\DateTimeImmutable $created_at): self
	{
		$this->created_at = $created_at;

		return $this;
	}
}
