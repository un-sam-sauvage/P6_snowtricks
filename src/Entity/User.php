<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 180, unique: true)]
	private ?string $username = null;

	#[ORM\Column]
	private array $roles = [];

	/**
	 * @var string The hashed password
	 */
	#[ORM\Column]
	private ?string $password = null;

	#[ORM\Column(type: 'boolean')]
	private $isVerified = false;

	#[ORM\OneToMany(mappedBy: 'author', targetEntity: Posts::class, orphanRemoval: true)]
	private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imgPath = null;

	public function __construct()
                        	{
                        		$this->posts = new ArrayCollection();
                          $this->comments = new ArrayCollection();
                        	}

	public function __toString()
                        	{
                        		return $this->username;
                        	}

	public function getId(): ?int
                        	{
                        		return $this->id;
                        	}

	public function getUsername(): ?string
                        	{
                        		return $this->username;
                        	}

	public function setUsername(string $username): self
                        	{
                        		$this->username = $username;
                        
                        		return $this;
                        	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string
                        	{
                        		return (string) $this->username;
                        	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
                        	{
                        		$roles = $this->roles;
                        		// guarantee every user at least has ROLE_USER
                        		$roles[] = 'ROLE_USER';
                        
                        		return array_unique($roles);
                        	}

	public function setRoles(array $roles): self
                        	{
                        		$this->roles = $roles;
                        
                        		return $this;
                        	}

	/**
	 * @see PasswordAuthenticatedUserInterface
	 */
	public function getPassword(): string
                        	{
                        		return $this->password;
                        	}

	public function setPassword(string $password): self
                        	{
                        		$this->password = $password;
                        
                        		return $this;
                        	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
                        	{
                        		// If you store any temporary, sensitive data on the user, clear it here
                        		// $this->plainPassword = null;
                        	}

	public function isVerified(): bool
                        	{
                        		return $this->isVerified;
                        	}

	public function setIsVerified(bool $isVerified): self
                        	{
                        		$this->isVerified = $isVerified;
                        
                        		return $this;
                        	}

	/**
	 * @return Collection<int, Posts>
	 */
	public function getPosts(): Collection
                        	{
                        		return $this->posts;
                        	}

	public function addPost(Posts $post): self
                        	{
                        		if (!$this->posts->contains($post)) {
                        			$this->posts->add($post);
                        			$post->setAuthor($this);
                        		}
                        
                        		return $this;
                        	}

	public function removePost(Posts $post): self
                        	{
                        		if ($this->posts->removeElement($post)) {
                        			// set the owning side to null (unless already changed)
                        			if ($post->getAuthor() === $this) {
                        				$post->setAuthor(null);
                        			}
                        		}
                        
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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getImgPath(): ?string
    {
        return $this->imgPath;
    }

    public function setImgPath(?string $imgPath): self
    {
        $this->imgPath = $imgPath;

        return $this;
    }
}
