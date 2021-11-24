<?php


class HomeController extends MainController
{
    public function zpracuj($parametry)
    {
        $this->data['name'] = $_SESSION['name'];


        $this->displayTwig();
    }


}