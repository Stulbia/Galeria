<?php

/**
 * Photo controller.
 */

namespace App\Controller;

use App\Dto\PhotoListInputFiltersDto;
use App\Entity\Comment;
use App\Entity\Photo;
use App\Form\Type\CommentType;
use App\Form\Type\PhotoEditType;
use App\Form\Type\PhotoType;
use App\Resolver\PhotoListInputFiltersDtoResolver;
use App\Service\CommentServiceInterface;
use App\Service\PhotoServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PhotoController.
 */
#[Route('/photo')]
class PhotoController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param PhotoServiceInterface   $photoService   Photo service
     * @param CommentServiceInterface $commentService Comment service
     * @param TranslatorInterface     $translator     Translator
     */
    public function __construct(private readonly PhotoServiceInterface $photoService, private readonly CommentServiceInterface $commentService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param PhotoListInputFiltersDto $filters Input filters
     * @param int                      $page    Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'photo_index', methods: 'GET')]
    public function index(#[MapQueryString(resolver: PhotoListInputFiltersDtoResolver::class)] PhotoListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->photoService->getPaginatedList($page, $filters);

        return $this->render('photo/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Photo $photo Photo entity
     * @param int   $page  Page number
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'photo_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Photo $photo, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->commentService->findByPhoto($photo, $page);

        return $this->render('photo/show.html.twig', ['photo' => $photo, 'pagination' => $pagination]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'photo_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $photo = new Photo();
        $photo->setAuthor($user);
        $photo->setFilename(" ");
        $form = $this->createForm(PhotoType::class, $photo, ['action' => $this->generateUrl('photo_create')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $this->photoService->save($photo, $file, $user);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'photo_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'photo')]
    public function edit(Request $request, Photo $photo): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PhotoEditType::class, $photo, [
            'method' => 'PUT',
            'action' => $this->generateUrl('photo_edit', ['id' => $photo->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->photoService->edit($photo);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/edit.html.twig', ['form' => $form->createView(), 'photo' => $photo]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'photo_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Photo $photo): Response
    {
        $form = $this->createForm(FormType::class, $photo, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('photo_delete', ['id' => $photo->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->photoService->delete($photo);

            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/delete.html.twig', ['form' => $form->createView(), 'photo' => $photo]);
    }

    /**
     * Leave a Comment.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/comment', name: 'comment_create', methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function comment(Request $request, Photo $photo): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $this->commentService->save($comment, $user, $photo);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('photo_show', ['id' => $photo->getId()]);
        }

        return $this->render('photo/comment.html.twig', ['form' => $form->createView(), 'photo' => $photo]);
    }
}
