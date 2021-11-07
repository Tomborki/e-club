<?php

class LoginController extends MainController
{
    public function zpracuj($parametry)
    {
        $this->twig->display('login.html.twig', ['name' => 'test']);
    }

}