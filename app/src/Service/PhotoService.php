<?php
/**
 * Photo service.
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\PhotoRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class PhotoService.
 */
class PhotoService implements PhotoServiceInterface
{
    /**
     * Constructor.
     *
     * @param string                     $targetDirectory   Target directory
     * @param PhotoRepository            $photoRepository   Photo repository
     * @param FileUploadServiceInterface $fileUploadService File upload service
     * @param Filesystem                 $filesystem        Filesystem component
     * @param PaginatorInterface         $paginator         Paginator
     */
    public function __construct(private readonly string $targetDirectory, private readonly PhotoRepository $photoRepository, private readonly FileUploadServiceInterface $fileUploadService, private readonly Filesystem $filesystem, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;
    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedUserList(int $page, User $author): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->photoRepository->queryByAuthor($author),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->photoRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save photo.
     *
     * @param Photo        $photo        Photo entity
     * @param UploadedFile $uploadedFile Uploaded file
     * @param User         $user         User entity
     */
    public function save(Photo $photo, UploadedFile $uploadedFile, User $user): void
    {
        $photoFilename = $this->fileUploadService->upload($uploadedFile);

        $photo->setAuthor($user);
        $photo->setFilename($photoFilename);
        $this->photoRepository->save($photo);
    }

    /**
     * Update photo.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Photo        $photo        Photo entity
     * @param User         $user         User entity
     */
    public function edit(UploadedFile $uploadedFile, Photo $photo, User $user): void
    {
        $filename = $photo->getFilename();

        if (null !== $filename) {
            $this->filesystem->remove(
                $this->targetDirectory.'/'.$filename
            );

            $this->save($photo, $uploadedFile, $user);
        }
    }

    /**
     * Delete photo.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void
    {
        $filename = $photo->getFilename();

        if (null !== $filename) {
            $this->filesystem->remove(
                $this->targetDirectory.'/'.$filename
            );
        }
        $this->photoRepository->delete($photo);
    }


    /**
     * Find by Galleery
     *
     * @param Gallery $gallery Gallery entity
     * @param int     $page    page
     *
     * @return PaginationInterface
     */
    public function findByGallery(Gallery $gallery, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->photoRepository->findByGallery($gallery),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }


    /**
     * Find Photos by Tag Name
     *
     * @param Tag[] $tagName Tag Name
     *
     * @return Photo[]
     */
    public function findByTags(array $tagName): array
    {
        return $this->photoRepository->findByTags($tagName);
    }
}
