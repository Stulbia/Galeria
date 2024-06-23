<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Service\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager Photo service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserManagerInterface $userManager, private readonly TranslatorInterface $translator)
    {
    }
    /**
     * Index action.
     *
     * @param int $page Page
     *
     * @return Response HTTP response
     */
    #[Route(name: 'user_index', methods: 'GET')]
    #[IsGranted('VIEW', subject: 'user')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            // Redirect to the home page or another page if the user is not an admin
            return $this->redirectToRoute('user_show');
        }
        $pagination = $this->userManager->getPaginatedList($page);

        return $this->render('user/index.html.twig', ['pagination' => $pagination]);
    }
    /**
     * Show action.
     *
     * @param User $user User
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'user_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[IsGranted('VIEW', subject: 'user')]
    public function show(User $user): Response
    {
        $thisUser = $this->getUser();

        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    // ...
    /**
     * Register action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/register',
        name: 'user_register',
        methods: 'GET|POST',
    )]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.registered_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/register.html.twig',
            ['form' => $form->createView()]
        );
    }

/** Edit action.
*
* @param Request $request HTTP request
* @param User    $user    User entity
*
* @return Response HTTP response
*/
    #[Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('VIEW', subject: 'user')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.registered_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }


    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'user_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, User $user): Response
    {
        if (!$this->userManager->canBeDeleted($user)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.user_contains_photos')
            );

            return $this->redirectToRoute('user_index');
        }

        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('user_delete', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->delete($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}