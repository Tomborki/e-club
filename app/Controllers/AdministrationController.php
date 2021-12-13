<?php


class AdministrationController extends MainController
{

    public function zpracuj($parametry)
    {
        $this->data['divisions'] = $this->db->getAllDivisions();

        if(isset($_GET['editedDivisionId'])){
            $info = $this->db->getDivisionById($_GET['editedDivisionId']);
            $this->data['idDivision'] = $_GET['editedDivisionId'];
            $this->data['divisionName'] = $info['nameDivision'];
            $this->data['chief'] = $info['chief'];
            $this->data['telContact'] = $info['telContact'];
            $this->data['emailContact'] = $info['emailContact'];
        }

        $this->displayTwig();
    }

    public function FORM_addNewDivision(){
        if(isset($_POST['submitAddDivisionForm'])){
            $name = $_POST['divisionName'];
            $chief = $_POST['divisionChief'];
            $email = $_POST['emailContact'];
            $tel = $_POST['telContact'];

            if($this->db->addDivision($name, $chief, $tel, $email)){
                $this->redirect(administration);
            }else{

            }
        }else{
            $this->redirect(administration);
        }
    }

    public function FORM_editDivision(){
        if(isset($_POST['submitEditDivisionForm'])){
            $name = $_POST['divisionName'];
            $chief = $_POST['divisionChief'];
            $email = $_POST['emailContact'];
            $tel = $_POST['telContact'];
            $idDivision = $_POST['divisionId'];

            if($this->db->editDivision($idDivision ,$name, $chief, $tel, $email)){
                $this->redirect(administration);
            }else{

            }
        }else{
            $this->redirect(administration);
        }
    }

    public function ACTION_deleteDivision($id){
        $this->db->deleteDivision($id);
        $this->redirect(administration);
    }

    public function ACTION_redirectToEdit($id){
        $this->redirect(administration . "/editDivision?editedDivisionId=" . $id);
    }

}