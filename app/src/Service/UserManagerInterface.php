<?php

/**
 * User Manager Interface.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserManager.
 */
interface UserManagerInterface
{
    /**
     * saves a new user
     *
     * @param UserInterface $user user
     *
     */
    public function register(UserInterface $user): void;

    /**
    /**
     * saves user data changes
     *
     * @param UserInterface $user user
     *
     */
    public function save(UserInterface $user): void;
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;
    /**
     * Change Password.
     *
     * @param User   $user             User entity
     * @param string $newPlainPassword New Plain Password
     */
    public function upgradePassword(UserInterface $user, string $newPlainPassword): void;
    /**
     * Verify Password.
     *
     * @param User   $user          User entity
     * @param string $plainPassword Plain Password
     *
     * @return bool
     */
    public function verifyPassword(UserInterface $user, string $plainPassword): bool;
}
