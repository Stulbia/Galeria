<?php

/**
 * User Manager Interface.
 */

namespace App\Service;



/**
 * Class UserManager.
 */
Interface UserManagerInterface
{


    function register($password, $email): void;

}

