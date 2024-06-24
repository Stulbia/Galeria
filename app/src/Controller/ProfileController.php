<?php
/**
 * Profile controller.
 */

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Form\Type\UserUpdateType;
use App\Service\UserManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
     * @param UserPasswordHasherInterface $passwordHasher PasswordHasher
     * @param UserManager                 $userManager    User service
     * @param TranslatorInterface         $translator     Translator
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly UserManager $userManager, private readonly TranslatorInterface $translator)
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
            try {
                    $password = $user->getPassword();
                    $user->setRoles([UserRole::ROLE_USER->value]);
                    $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                    $user->setPassword($hashedPassword);
                    $this->userManager->save($user);

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
            UserUpdateType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('self_edit'),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userManager->save($user);
                $this->addFlash(
                    'success',
                    $this->translator->trans('message.updated_successfully')
                );
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Email in use.');

                return $this->redirectToRoute('self_edit');
            } catch (Exception $e) {
                $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            }

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
}
