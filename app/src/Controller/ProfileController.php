<?php
/**
 * Profile controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Service\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProfileController.
 */
class ProfileController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserManagerInterface $userManager, private readonly TranslatorInterface $translator)
    {
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
            $this->userManager->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.registered_successfully')
            );

            return $this->redirectToRoute('user_profile');
        }

        return $this->render(
            'profile/register.html.twig',
            ['form' => $form->createView()]
        );
    }
    /**
     * View own profile action.
     *
     * @return Response HTTP response
     */
    #[Route('/profile', name: 'user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function profile(): Response
    {
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', ['user' => $user]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/profile/edit', name: 'self_edit', methods: ['GET', 'PUT'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('self_edit'),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.updated_successfully')
            );

            return $this->redirectToRoute('user_profile');
        }

        return $this->render(
            'profile/edit.html.twig',
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
     *
     * @return Response HTTP response
     */
    #[Route('/profile/delete', name: 'self_delete', methods: ['GET', 'DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request): Response
    {
        $user = $this->getUser();

        if (!$this->userManager->canBeDeleted($user)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.user_has_photos')
            );

            return $this->redirectToRoute('user_profile');
        }

        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('self_delete'),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->delete($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('app_logout');
        }

        return $this->render(
            'profile/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
