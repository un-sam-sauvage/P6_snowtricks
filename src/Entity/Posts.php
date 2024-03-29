<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a post with this slug')]
class Posts
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255, unique: true)]
	#[Assert\NotBlank]
	private ?string $name = null;

	#[ORM\Column(length: 255)]
	#[Assert\NotBlank]
	private ?string $content = null;

	#[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'posts')]
	private Collection $categories;

	#[ORM\ManyToOne(inversedBy: 'posts')]
	#[ORM\JoinColumn(nullable: false)]
	#[Assert\NotBlank]
	private ?User $author = null;

	#[ORM\OneToMany(mappedBy: 'posts', targetEntity: Comments::class, orphanRemoval: true)]
	private Collection $comments;

	#[ORM\OneToMany(mappedBy: 'post', targetEntity: Media::class, orphanRemoval: true)]
	private Collection $medias;

	#[ORM\Column (options: ["default" => "CURRENT_TIMESTAMP"])]
	private ?\DateTimeImmutable $created_at;

	#[ORM\Column(length: 255, unique: true)]

	private ?string $slug = null;

	public function __construct()
		 		 	{
		 		 		$this->categories = new ArrayCollection();
		 		 		$this->comments = new ArrayCollection();
		 		 		$this->medias = new ArrayCollection();
		 				$this->created_at = new DateTimeImmutable();
		 		 	}
	public function __toString()
		 		 	{
		 		 		return $this->name;
		 		 	}
	public function getId(): ?int
		 		 	{
		 		 		return $this->id;
		 		 	}

	public function getName(): ?string
		 		 	{
		 		 		return $this->name;
		 		 	}

	public function setName(string $name): self
		 		 	{
		 		 		$this->name = $name;
		 		 	
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

	/**
	 * @return Collection<int, Category>
	 */
	public function getCategories(): Collection
		 		 	{
		 		 		return $this->categories;
		 		 	}

	public function addCategory(Category $category): self
		 		 	{
		 		 		if (!$this->categories->contains($category)) {
		 		 			$this->categories->add($category);
		 		 		}
		 		 	
		 		 		return $this;
		 		 	}

	public function removeCategory(Category $category): self
		 		 	{
		 		 		$this->categories->removeElement($category);
		 		 	
		 		 		return $this;
		 		 	}

	public function getAuthor(): ?User
		 		 	{
		 		 		return $this->author;
		 		 	}

	public function setAuthor(?User $author): self
		 		 	{
		 		 		$this->author = $author;
		 		 	
		 		 		return $this;
		 		 	}

	/**
	 * @return Collection<int, Comments>
	 */
	public function getComments(): Collection
		 		 	{
		 		 		return $this->comments;
		 		 	}

	public function addComment(Comments $comment): self
		 		 	{
		 		 		if (!$this->comments->contains($comment)) {
		 		 			$this->comments->add($comment);
		 		 			$comment->setPosts($this);
		 		 		}
		 		 	
		 		 		return $this;
		 		 	}

	public function removeComment(Comments $comment): self
	{
		if ($this->comments->removeElement($comment)) {
			// set the owning side to null (unless already changed)
			if ($comment->getPosts() === $this) {
				$comment->setPosts(null);
			}
		}
	
		return $this;
	}

	/**
	 * @return Collection<int, Media>
	 */
	public function getMedias(): Collection
	{
		return $this->medias;
	}

	public function getFirstPicture() {
		$return = null;
		foreach($this->medias as $media) {
			if (!$media->isIsVideo()) {
				$return = $media;
				break;
			}
		}
		return $return;
	}

	public function addMedia(Media $media): self
	{
		if (!$this->medias->contains($media)) {
			$this->medias->add($media);
			$media->setPost($this);
		}
	
		return $this;
	}

	public function removeMedia(Media $media): self
	{
		if ($this->medias->removeElement($media)) {
			// set the owning side to null (unless already changed)
			if ($media->getPost() === $this) {
				$media->setPost(null);
			}
		}
	
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

	public function getSlug(): ?string
	{
		return $this->slug;
	}

	public function setSlug(string $slug): self
	{
		$this->slug = $slug;

		return $this;
	}
}
