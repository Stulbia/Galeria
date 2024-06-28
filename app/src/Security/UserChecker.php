<?php
/**
 * UserChecker
 */
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * Class UserChecker
 *
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Checks the user before authentication.
     *
     * @param UserInterface $user The user being authenticated.
     *
     * @throws CustomUserMessageAuthenticationException If the user is banned.
     */
    public function checkPreAuth(UserInterface $user):void
    {
        if ($user instanceof User && $user->isBanned()) {
            throw new CustomUserMessageAuthenticationException('message.banned');
        }
    }

    /**
     * Checks the user after authentication.
     *
     * @param UserInterface $user The authenticated user.
     */
    public function checkPostAuth(UserInterface $user)
    {
    }
}
