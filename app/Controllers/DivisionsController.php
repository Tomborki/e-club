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

        if(isset($_POST['addDivisionForm'])){
            echo 'xd';
        }


        $this->displayTwig();
    }

    public function ACTION_likeDivision($id){
        $this->db->userLikedDivision($_SESSION['userID'], $id);
        $_SESSION['userDivision'] = $id;
        $this->redirect(divisions);
    }

    public function ACTION_unLikeDivision(){
        $this->db->unLikeDivision($_SESSION['userID']);
        $_SESSION['userDivision'] = null;
        $this->redirect(divisions);
    }

    public function FORM_addNewDivision(){
        $name = $_POST['divisionName'];
        $chief = $_POST['divisionChief'];
        $email = $_POST['emailContact'];
        $tel = $_POST['telContact'];

    }

}