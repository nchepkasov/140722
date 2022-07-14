<?php

namespace App;

class BasicView
{
    private $path;

    public function __construct (string $name)
    {
        $this->path = APP_DIR . '/views/' . $name . '.phtml';
    }

    public function render (array $vars = [])
    {
        $isLoggedIn = \App\Services\Auth::isLoggedIn ();
        $csrfToken = \App\Helpers\CSRF::getToken ();
        
        extract ($vars);
        ob_start ();
        
        require ($this->path);

        $view = ob_get_contents ();        
        ob_end_clean ();

        return $view;
    }
}
