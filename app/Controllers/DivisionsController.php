<?php

use \Tamtamchik\SimpleFlash\Flash;

class DivisionsController extends MainController
{

    public function zpracuj($parametry)
    {
        $this->data['divisions'] = $this->db->getAllDivisions();

        if(isset($_SESSION['userDivision'])){
            $this->data['userDivision'] = $_SESSION['userDivision'];
        }else{
            $this->data['userDivision'] = null;
        }

        $this->displayTwig();
    }

    public function ACTION_likeDivision($id){
        if($this->db->userLikedDivision($_SESSION['userID'], $id)){
            $_SESSION['userDivision'] = $id;
            $division = $this->db->getDivisionById($id);
            Flash::success('Oblíbil jste si oddíl: ' . $division['nameDivision']);
        }else{
            Flash::error('Něco se pokazilo');
        }

        $this->redirect(divisions);
    }

    public function ACTION_unLikeDivision(){
        if($this->db->unLikeDivision($_SESSION['userID'])){
            $_SESSION['userDivision'] = null;
            Flash::success('Odoblíbil jste si oddíl');
        }else{
            Flash::error('Něco se pokazilo');
        }

        $this->redirect(divisions);
    }
}