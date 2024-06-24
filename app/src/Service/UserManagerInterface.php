<?php

/**
 * User Manager Interface.
 */

namespace App\Service;



use App\Entity\User;

/**
 * Class UserManager.
 */
Interface UserManagerInterface
{


    public function save(User $user, ):void;

}

