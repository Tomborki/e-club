<?php

use \Tamtamchik\SimpleFlash\Flash;

class DivisionsController extends MainController
{

    public function zpracuj($parametry)
    {
        $allDivisions = $this->db->getAllDivisions();
        $j = 0;

        $this->data['allMessages'] = $this->db->getAllMessagesByDivisions();

        foreach ($allDivisions as $division){
            $allDivisions[$j]['chief'] = $this->db->getUserById($division['chiefUserId']);
            $j++;
        }

        $this->data['divisions'] = $allDivisions;

        if(isset($_SESSION['userDivision'])){
            $this->data['userDivision'] = $_SESSION['userDivision'];
        }else{
            $this->data['userDivision'] = null;
        }

        $this->displayTwig();
    }

    /**
     * @param $id
     * Akce lajkne oddil uzivateli
     */
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

    /**
     * Akce "odlajkne" oddil uzivateli
     */
    public function ACTION_unLikeDivision(){
        if($this->db->unLikeDivision($_SESSION['userID'])){
            $_SESSION['userDivision'] = null;
            Flash::success('Odoblíbil jste si oddíl');
        }else{
            Flash::error('Něco se pokazilo');
        }

        $this->redirect(divisions);
    }

    public function FORM_addNewMessage(){
        if(isset($_POST['submitAddMessForm'])){
            $content = $_POST['messContent'];
            $title = $_POST['messTitle'];
            $chiefId = $_POST['chiefId'];
            $divisionId = $_POST['divisionId'];

            if($this->db->addMessage($title, $content, $chiefId, $divisionId)){
                Flash::success('Zpráva byla přidána');
            }else{
                Flash::error('Zprávu se nepodařilo přidat');
            }

            $this->redirect(divisions);
        }else{
            $this->redirect(divisions);
        }
    }

    /**
     * @param $idMess
     * Akce odebira aktualitu podle jeho id
     */
    public function ACTION_removeMess($idMess){

        $mess = $this->db->getMessageById($idMess);

        if($mess['chiefId'] == $_SESSION['userID']){
            if($this->db->removeMessById($idMess)){
                Flash::success('Zpráva byla odebrána');
            }else{
                Flash::error('Zprávu se nepovedlo odebrat');
            }
            $this->redirect(divisions);
        }else{
            $this->redirect(divisions);
        }




    }
}