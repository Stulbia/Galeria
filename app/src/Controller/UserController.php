<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\UserType;
use App\Form\Type\UserTypeForAdmin;
use App\Form\Type\UserUpdateType;
use App\Service\PhotoServiceInterface;
use App\Service\UserManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
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
     * @param UserManagerInterface  $userManager  User service
     * @param TranslatorInterface   $translator   Translator
     * @param PhotoServiceInterface $photoService Photo Service
     */
    public function __construct(private readonly UserManagerInterface $userManager, private readonly TranslatorInterface $translator, private readonly PhotoServiceInterface $photoService)
    {
    }


    /**
     * Show user profile with photo list.
     *
     * @param int $page Page
     *
     * @return Response HTTP response
     */
    #[Route(name: 'user_index', methods: ['GET'])]
    #[isGranted('IS_AUTHENTICATED_FULLY')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $user = $this ->getUser();
        $pagination = $this->photoService->getPaginatedUserList($page, $user);

        return $this->render('user/index.html.twig', ['pagination' => $pagination, 'user'  => $user]);
    }
    /**
     * Register action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/register', name: 'user_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setRoles([UserRole::ROLE_USER->value]);
                $this->userManager->register($user);

                $this->addFlash(
                    'success',
                    $this->translator->trans('message.registered_successfully')
                );
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'message.Email in use.');

                return $this->redirectToRoute('user_register');
            } catch (Exception $e) {
                $this->addFlash('error', 'message.An error occurred: '.$e->getMessage());
            }

            return $this->redirectToRoute('photo_index');
        }

        return $this->render(
            'user/register.html.twig',
            ['form' => $form->createView()]
        );
    }
    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('EDIT', subject:'user')]
    public function edit(Request $request, User $user): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
             $id = $user->getId();

             return $this->redirectToRoute('user_edit_admin', ['id' => $id ]);
        }
        $form = $this->createForm(
            UserUpdateType::class,
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
                $this->translator->trans('message.updated_successfully')
            );

            return $this->redirectToRoute('user_list');
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
     * List users.
     *
     * @param int $page Page
     *
     * @return Response HTTP response
     */
    #[Route('/list', name: 'user_list', methods: ['GET'])]
    #[isGranted('ROLE_ADMIN')]
    public function list(#[MapQueryParameter] int $page = 1): Response
    {
//        if (!$this->isGranted('ROLE_ADMIN')) {
//            $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId()]);
//        }
        $pagination = $this->userManager->getPaginatedList($page);

        return $this->render('user/list.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param User $user User
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'user_show', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    #[IsGranted('VIEW', subject: 'user')]
    public function show(User $user): Response
    {
//        if (!$this->isGranted('VIEW', subject: 'user')) {
//            $this->redirectToRoute('user_show', ['id' => $user->getId()]);
//        }

        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit/admin', name: 'user_edit_admin', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editAdmin(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserTypeForAdmin::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit_admin', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.updated_successfully')
            );

            return $this->redirectToRoute('user_list');
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
    #[Route('/{id}/delete', name: 'user_delete', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'DELETE'])]
    #[IsGranted('DELETE', subject: 'user')]
    public function delete(Request $request, User $user): Response
    {
        if (!$this->userManager->canBeDeleted($user)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.user_has_photos')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
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

            return $this->redirectToRoute('user_list');
        }

        return $this->render(
            'user/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }


    /**
     * Change Password.
     *
     * @param Request $request HTTP request
     * @param User    $user    User
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/password', name: 'user_password', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('EDIT', subject:'user')]
    public function changePassword(Request $request, User $user): Response
    {
        $form = $this->createForm(
            ChangePasswordType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_password', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'New passwords do not match.');
            } else {
                try {
                    $this->userManager->upgradePassword($user, $newPassword);
                    $this->addFlash('success', 'Password updated successfully.');

                    return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'An error occurred while updating the password: '.$e->getMessage());
                }
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
