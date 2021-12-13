<?php


class AdministrationController extends MainController
{

    public function zpracuj($parametry)
    {
        $this->data['divisions'] = $this->db->getAllDivisions();
        $this->data['users'] = $this->db->getAllUsers();
        $this->data['roles'] = $this->db->getAllRoles();

        for ($i = 0; $i < count($this->data['users']); $i++) {
            $this->data['users'][$i]['roleText'] = $this->db->getRoleById($this->data['users'][$i]['idRole'])['roleName'];
        }

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

    public function FORM_addNewUser(){
        if(isset($_POST['submitAddUserForm'])){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $realName = $_POST['realName'];
            $realSurname = $_POST['realSurname'];
            $email = $_POST['email'];
            $tel = $_POST['tel'];
            $role = $_POST['role'];

            if($this->db->addUser($username, $password, $realName, $realSurname, $email, $tel, $role)){
                $this->redirect(administration);
            }else{

            }

            /*
            $errors= array();
            $file_name = $_FILES['avatar']['name'];
            $file_size =$_FILES['avatar']['size'];
            $file_tmp =$_FILES['avatar']['tmp_name'];
            $file_type=$_FILES['avatar']['type'];
            $file_ext_temp = explode('.',$_FILES['avatar']['name']);
            $file_ext= strtolower(end($file_ext_temp));

            $extensions= array("jpeg","jpg","png");

            if(in_array($file_ext,$extensions)=== false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }

            if($file_size > 2097152){
                $errors[]='File size must be excately 2 MB';
            }

            if(empty($errors)==true){
                move_uploaded_file($file_tmp,"/data/userAvatars/" . $lastInseredID . "/avatar" . $file_ext );
            }else{
                print_r($errors);
            }
            */



        }else{
            $this->redirect(administration);
        }
    }

    public function ACTION_deleteUser($id){
        $this->db->deleteUser($id);
        $this->redirect(administration);
    }

    public function ACTION_deleteDivision($id){
        $this->db->deleteDivision($id);
        $this->redirect(administration);
    }

    public function ACTION_redirectToEdit($id){
        $this->redirect(administration . "/editDivision?editedDivisionId=" . $id);
    }

}