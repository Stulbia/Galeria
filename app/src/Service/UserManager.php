<?php
/**
 * User Manager.
 */

namespace App\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



/**
 * Class UserManager.
 */ class UserManager implements UserManagerInterface
{
/**
 * @param UserPasswordHasherInterface $passwordHasher Password hasher
 */
public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
{
}
    function register($password, $email):void
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([UserRole::ROLE_USER->value]);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password
            )
        );
        }}

