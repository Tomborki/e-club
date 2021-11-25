<?php

class Error404Controller extends MainController
{
    public function zpracuj($parametry)
    {
        $this->data['test'] = 'xd';
        $this->displayTwig();
    }
}