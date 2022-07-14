<?php 

namespace App\Helpers;

class Redirect
{
    public static function to (string $location)
    {
        Header ("Location: $location");
        
        exit ();
    }

    public static function toRef ()
    {
        Header ('Location: ' . $_SERVER ['HTTP_REFERER']);

        exit ();
    }
}