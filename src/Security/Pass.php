<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Namespace
|---------------------------------------------------
*/
namespace Kiaan\Security;

/*
|---------------------------------------------------
| Password
|---------------------------------------------------
*/
class Pass {

    /**
     * Create Password
    */
    public function hash($pass)
    {
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        return $hash;
    }

    /**
     * Verify Password
    */
    public function verify($pass, $hash)
    {
        $verify = password_verify($pass, $hash);

        return $verify;
    }

}