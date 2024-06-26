<?php
/**
* Comment controller.
*/

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use App\Service\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
* Class CommentController.
*/
#[Route('/comment')]
class CommentController extends AbstractController
{
/**
* Constructor.
*
* @param CommentServiceInterface $commentService Photo service
* @param TranslatorInterface     $translator     Translator
*/
    public function __construct(private readonly CommentServiceInterface $commentService, private readonly TranslatorInterface $translator)
    {
//        $this->commentService = $photoService;
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'comment_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'comment')]
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(
            CommentType::class,
            $comment,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('comment_edit', ['id' => $comment->getId(), 'comment' => $comment->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $comment->getUser();
            $photo = $comment->getPhoto();
            $this->commentService->save($comment, $user, $photo);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('photo_show', ['id' => $comment->getPhoto()->getId()]);
        }

        return $this->render(
            'comment/edit.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );
    }

    /**
     * Index action.
     *
     * @param  int $page page
     *
     * @return Response HTTP response
     */
    #[Route(name: 'comment_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->commentService->getPaginatedList($page);

        return $this->render('comment/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Comment $comment Comment
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'comment_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', ['comment' => $comment]);
    }

//    // ...
//    /**
//     * Create action.
//     *
//     * @param Request $request HTTP request
//     *
//     * @return Response HTTP response
//     */
//    #[Route(
//        '/create',
//        name: 'comment_create',
//        methods: 'GET|POST',
//    )]
//    #[IsGranted('ROLE_USER')]
//    public function create(Request $request): Response
//    {
//        $comment = new Comment();
//        $form = $this->createForm(CommentType::class, $comment);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $user = $this->getUser();
//            $this->commentService->save($comment, $user, $photo);
//
//            $this->addFlash(
//                'success',
//                $this->translator->trans('message.created_successfully')
//            );
//
//            return $this->redirectToRoute('comment_index');
//        }
//
//        return $this->render(
//            'comment/create.html.twig',
//            ['form' => $form->createView()]
//        );
//    }


    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'comment_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'comment')]
    public function delete(Request $request, Comment $comment): Response
    {

//        $form = $this->createForm(
//            FormType::class,
//            $comment,
//            [
//                'method' => 'DELETE',
//                'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
//            ]
//        );
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->delete($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('photo_show', ['id' => $comment->getPhoto()->getId()]);
//        }
//
//        return $this->render(
//            'comment/delete.html.twig',
//            [
//                'form' => $form->createView(),
//                'comment' => $comment,
//            ]
//        );
    }
}
