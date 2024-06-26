<?php
/**
 * Comment entity.
 */
namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTimeImmutable;

/**
 * Class Comment.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
{

    /**
     * Primary key.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    /**
     * Created at.
     *
     * @var DateTimeImmutable
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;

    /**
     * Updated at.
     *
     * @var DateTimeImmutable
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTimeImmutable $updatedAt;

    /**
     * Content.
     *
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private string $content;

    /**
     * Photo.
     *
     * @var Photo|null
     */
    #[ORM\ManyToOne(targetEntity: Photo::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Photo $photo = null;


    /**
     * User
     *
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for created at.
     *
     * @return DateTimeImmutable Created at
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param DateTimeImmutable $createdAt Created at
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for updated at.
     *
     * @return DateTimeImmutable Updated at
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param DateTimeImmutable|null $updatedAt Updated at
     */
    public function setUpdatedAt(?DateTimeImmutable $updatedAt):void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for content.
     *
     * @return string|null content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string|null $content content
     *
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }


    /**
     * Getter for photo.
     *
     * @return Photo|null $photo
     */
    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }
    /**
     * Setter for photo.
     *
     * @param Photo|null $photo Photo
     *
     */
    public function setPhoto(?Photo $photo): void
    {
        $this->photo = $photo;
    }


    /**
     * Getter for User.
     *
     * @return User $user User
     *
     */
    public function getUser():User
    {
        return $this->user;
    }

    /**
     * Setter for User.
     *
     * @param User $user Photo
     *
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
