<?php

namespace App\Services;

class Auth
{
    public static function isLoggedIn ()
    {
        return (bool) $_SESSION ['logged_in'];
    }

    public static function getUID ()
    {
        return $_SESSION ['user_id'];
    }
    
    public static function doLogin ($login, $password): bool
    {
        $stmt = \App\Database::getConn ()->prepare ("SELECT `id`, `password` FROM `users` WHERE `login` = ?");

        $stmt->bindParam (1, $login);

        $cols = $stmt->execute ()->fetch ();
        
        if ($cols ['password'] !== false && password_verify ($password, $cols ['password']))
        {
            $_SESSION ['logged_in'] = true;
            $_SESSION ['user_id']   = $cols ['id'];


            return true;
        }

        return false;
    }

    public static function doLogout ()
    {
        session_destroy ();
    }
}