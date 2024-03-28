<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index as Index;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
/**
 * @ORM\Table(name="videos", indexes={@Index(name="title_idx", columns={"title"})})
 */
class Video
{
    public const videoForNotLoggedIn = 113716040; // vimeo id
    public const VimeoPath = 'https://player.vimeo.com/video/';
    public const perPage = 5; // for pagination
  
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'video')]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'likedVideos')]
    #[ORM\JoinTable(name: 'likes')]
    private Collection $userThatLike;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'dislikedVideos')]
    #[ORM\JoinTable(name: 'dislikes')]
    private Collection $usersThatDontLike;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->userThatLike = new ArrayCollection();
        $this->usersThatDontLike = new ArrayCollection();
    }

    #[ORM\OneToMany(mappedBy: 'video')]


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getVimeoId($user): ?string
    {
        if ($user) {
            return $this->path;
        } else return self::VimeoPath . self::videoForNotLoggedIn;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setVideo($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getVideo() === $this) {
                $comment->setVideo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserThatLike(): Collection
    {
        return $this->userThatLike;
    }

    public function addUserThatLike(User $userThatLike): static
    {
        if (!$this->userThatLike->contains($userThatLike)) {
            $this->userThatLike->add($userThatLike);
        }

        return $this;
    }

    public function removeUserThatLike(User $userThatLike): static
    {
        $this->userThatLike->removeElement($userThatLike);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersThatDontLike(): Collection
    {
        return $this->usersThatDontLike;
    }

    public function addUsersThatDontLike(User $usersThatDontLike): static
    {
        if (!$this->usersThatDontLike->contains($usersThatDontLike)) {
            $this->usersThatDontLike->add($usersThatDontLike);
        }

        return $this;
    }

    public function removeUsersThatDontLike(User $usersThatDontLike): static
    {
        $this->usersThatDontLike->removeElement($usersThatDontLike);

        return $this;
    }
}
