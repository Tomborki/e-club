<?php

class LoginController extends MainController
{
    public function zpracuj($parametry)
    {
        $this->data['hello'] = 'e-Club';

        $this->displayTwig();
    }

}