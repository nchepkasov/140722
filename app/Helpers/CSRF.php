<?php
 
namespace App\Helpers;

class CSRF
{
    public static function getToken ()
    {
        if (!isset ($_SESSION ['csrf_token']))
        {
            $_SESSION ['csrf_token'] = \App\Helpers\StringUtils::getRandomString (32);
        }

        return $_SESSION ['csrf_token'];
    }

    public static function validate ()
    {
        return $_REQUEST ['csrf'] == self::getToken ();
    }
}