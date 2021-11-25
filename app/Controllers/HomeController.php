<?php


class HomeController extends MainController
{
    private $role = array(1,2);

    public function zpracuj($parametry)
    {
        $this->data['name'] = $_SESSION['name'];



        $this->displayTwig();
    }

}