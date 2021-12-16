<?php


class AdministrationController extends MainController
{

    public function zpracuj($parametry)
    {
        $this->data['divisions'] = $this->db->getAllDivisions();
        $this->data['users'] = $this->db->getAllUsers();
        $this->data['roles'] = $this->db->getAllRoles();
        $this->data['typeFines'] = $this->db->getAllTypeFines();

        for ($i = 0; $i < count($this->data['users']); $i++) {
            $this->data['users'][$i]['roleText'] = $this->db->getRoleById($this->data['users'][$i]['idRole'])['roleName'];
        }

        if(isset($_GET['editedDivisionId'])){
            $this->data['editDivision'] = $this->db->getDivisionById($_GET['editedDivisionId']);
        }

        if(isset($_GET['editedUserId'])){
            $this->data['editUser'] = $this->db->getUserById($_GET['editedUserId']);
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

    public function FORM_addNewFine(){
        if(isset($_POST['submitAddFineForm'])){
            $name = $_POST['fineName'];
            $money = $_POST['money'];

            if($this->db->addFineType($name, $money)){
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

            $errors= array();
            $file_name = $_FILES['avatar']['name'];
            $file_size =$_FILES['avatar']['size'];
            $file_tmp =$_FILES['avatar']['tmp_name'];
            $file_type= $_FILES['avatar']['type'];
            $file_ext_temp = explode('.',$file_name);
            $file_ext= strtolower(end($file_ext_temp));

            $avatarName = 'avatar' . bin2hex(random_bytes(20)) . '.' . $file_ext;

            $lastInseredID = $this->db->addUser($username, $password, $realName, $realSurname, $email, $tel, $avatarName, $role);

            $extensions= array("jpeg","jpg","png");

            if(in_array($file_ext,$extensions)=== false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }

            if($file_size > 2097152){
                $errors[]='File size must be excately 2 MB';
            }

            if(empty($errors)==true){
                $structure = './data/userAvatars/' . $lastInseredID . '/';
                if (!mkdir($structure, 0777, true)) {
                    die('Failed to create directories...');
                }else{
                    move_uploaded_file($file_tmp,"./data/userAvatars/" . $lastInseredID . "/" . $avatarName);
                    $this->redirect(administration);
                }
            }else{
                print_r($errors);
            }
        }else{
            $this->redirect(administration);
        }
    }

    public function FORM_editUser(){
        if(isset($_POST['submitEditUserForm'])){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $realName = $_POST['realName'];
            $realSurname = $_POST['realSurname'];
            $email = $_POST['email'];
            $tel = $_POST['tel'];
            $role = $_POST['role'];
            $userID = $_POST['editedUserId'];

            if($_FILES['avatar']['name'] != null){
                $errors= array();
                $file_name = $_FILES['avatar']['name'];
                $file_size =$_FILES['avatar']['size'];
                $file_tmp =$_FILES['avatar']['tmp_name'];
                $file_type= $_FILES['avatar']['type'];
                $file_ext_temp = explode('.',$file_name);
                $file_ext= strtolower(end($file_ext_temp));

                $avatarName = 'avatar' . bin2hex(random_bytes(20)) . '.' . $file_ext;

                if(empty($errors)==true){
                    $structure = './data/userAvatars/' . $userID . '/';

                        $this->deleteFiles($structure);
                        move_uploaded_file($file_tmp,"./data/userAvatars/" . $userID . "/" . $avatarName);
                        $this->db->changeUserAvatarImageName($userID, $avatarName);
                        $this->redirect(administration);

                }else{
                    print_r($errors);
                }
            }

            if($_POST['password'] != null){
                if($password == $_POST['password2']){
                    $this->db->changeUserPassword($userID, $password);
                }
            }

            $this->db->editUserWithoutPassAvatar($userID, $username, $realName, $realSurname, $email, $tel, $role);
            $this->redirect(administration);

        }else{
            $this->redirect(administration);
        }
    }

    // -------------------- AKCE ------------------------------------------------

    /**
     * @param $id
     * Akce odstranuje uzivatele podle id
     */
    public function ACTION_deleteUser($id){
        $this->db->deleteUser($id);
        $this->deleteDir('./data/userAvatars/' . $id);
        $this->redirect(administration);
    }

    /**
     * @param $id
     * Akce poveruje uzivatele pokladnikem podle id
     */
    public function ACTION_entrustCashier($id){
        $this->db->entrustUserToCashier($id);
        $this->redirect(administration);
    }

    /**
     * @param $id
     * Akce odpoveri uzivatele pokladnikem po id
     */
    public function ACTION_disableCashier($id){
        $this->db->unEntrustUserToCashier($id);
        $this->redirect(administration);
    }

    /**
     * @param $id
     * Akce odstranuje oddil podle id
     */
    public function ACTION_deleteDivision($id){
        $this->db->deleteDivision($id);
        $this->redirect(administration);
    }

    /**
     * @param $id
     * Akce presmeruje stranku stranku na editaci oddilu
     */
    public function ACTION_redirectToEditDivision($id){
        $this->redirect(administration . "/editDivision?editedDivisionId=" . $id);
    }

    /**
     * @param $id
     * Akce presmeruje stranku stranku na editaci uzivatele
     */
    public function ACTION_redirectToEditUser($id){
        $this->redirect(administration . "/editUser?editedUserId=" . $id);
    }

    /**
     * @param $id
     * Akce odstrani typ pokuty podle id
     */
    public function ACTION_deleteFineType($id){
        $this->db->deleteFineType($id);
        $this->redirect(administration);
    }

}