<?php

use \Tamtamchik\SimpleFlash\Flash;

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

        if(isset($_GET['editedFineTypeId'])){
            $this->data['editTypeFine'] = $this->db->getFineTypeById($_GET['editedFineTypeId']);
        }

        $this->displayTwig();
    }

    /**
     * Funce zpracovava odpovedi od formulare addNewDivision
     * Formular na pridavani noveho oddilu
     */
    public function FORM_addNewDivision(){
        if(isset($_POST['submitAddDivisionForm'])){
            $name = $_POST['divisionName'];
            $chief = $_POST['divisionChief'];
            $email = $_POST['emailContact'];
            $tel = $_POST['telContact'];

            if($this->db->addDivision($name, $chief, $tel, $email)){
                Flash::success('Oddíl úspěšně přidán');
                $this->redirect(administration);
            }else{
                Flash::error('Nepodařilo se přidat oddíl');
                $this->redirect(administration);
            }
        }else{
            $this->redirect(administration);
        }
    }

    /**
     * Funkce zpracovava odpovedi od formulare addNewFine
     * Formular na pridavani noveho typu pokuty
     */
    public function FORM_addNewFine(){
        if(isset($_POST['submitAddFineForm'])){
            $name = $_POST['fineName'];
            $money = $_POST['money'];

            if($this->db->addFineType($name, $money)){
                Flash::success('Pokuta úspěšně přidána');
                $this->redirect(administration);
            }else{
                Flash::error('Nepovedlo se přidat novou pokutu');
                $this->redirect(administration);
            }
        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect(administration);
        }
    }

    /**
     * Funkce zpracovava odpovedi od formulare editDivision
     * Formular na editovani oddilu
     */
    public function FORM_editDivision(){
        if(isset($_POST['submitEditDivisionForm'])){
            $name = $_POST['divisionName'];
            $chief = $_POST['divisionChief'];
            $email = $_POST['emailContact'];
            $tel = $_POST['telContact'];
            $idDivision = $_POST['divisionId'];

            if($this->db->editDivision($idDivision ,$name, $chief, $tel, $email)){
                Flash::success('Oddíl úspěšně upraven');
                $this->redirect(administration);
            }else{
                Flash::error('Oddíl se nepovedlo upravit');
                $this->redirect(administration);
            }
        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect(administration);
        }
    }

    /**
     * @throws Exception
     * Funkce zpracovava odpovedi od formulare addNewUser
     * Formular na pridavani noveho uzivatele
     */
    public function FORM_addNewUser(){
        if(isset($_POST['submitAddUserForm'])){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $realName = $_POST['realName'];
            $realSurname = $_POST['realSurname'];
            $email = $_POST['email'];
            $tel = $_POST['tel'];
            $role = $_POST['role'];

            $allUsers = $this->db->getAllUsers();
            foreach ($allUsers as $user){
                if($user['username'] == $username){
                    Flash::error('Uživatelské jméno "' . $username . '" je již zabrané!');
                    $this->redirect('administration/addUser');
                }
            }

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
                $errors[]="Špatný typ obrázku";
            }

            if($file_size > 2097152){
                $errors[]='Obrázek musí být menší nebo roven velikosti 2MB';
            }

            if(empty($errors)==true){
                $structure = './data/userAvatars/' . $lastInseredID . '/';
                if (!mkdir($structure, 0777, true)) {
                    die('Failed to create directories...');
                }else{
                    move_uploaded_file($file_tmp,"./data/userAvatars/" . $lastInseredID . "/" . $avatarName);
                    Flash::success('Uživatel úspěšně přidán');
                    $this->redirect(administration);
                }
            }else{
                Flash::error($errors);
                $this->redirect('administration/addUser');
            }
        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect(administration);
        }
    }

    /**
     * @throws Exception
     * Funkce zpracovava odpovedi od formulare editUser
     * Formular na upravovani uzivatele
     */
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

            $allUsers = $this->db->getAllUsers();
            foreach ($allUsers as $user){
                if($user['username'] == $username && $userID != $user['id']){
                    Flash::error('Uživatelské jméno "' . $username . '" je již zabrané!');
                    $this->redirect('administration/editUser?editedUserId=' . $userID);
                }
            }

            if($_FILES['avatar']['name'] != null){
                $errors= array();
                $file_name = $_FILES['avatar']['name'];
                $file_size =$_FILES['avatar']['size'];
                $file_tmp =$_FILES['avatar']['tmp_name'];
                $file_type= $_FILES['avatar']['type'];
                $file_ext_temp = explode('.',$file_name);
                $file_ext= strtolower(end($file_ext_temp));

                $avatarName = 'avatar' . bin2hex(random_bytes(20)) . '.' . $file_ext;

                $extensions= array("jpeg","jpg","png");

                if(in_array($file_ext,$extensions)=== false){
                    $errors[]="Špatný typ obrázku";
                }

                if($file_size > 2097152){
                    $errors[]='Obrázek musí být menší nebo roven velikosti 2MB';
                }

                if(empty($errors)==true){
                    $structure = './data/userAvatars/' . $userID . '/';
                    if(is_dir($structure)){
                        $this->deleteFiles($structure);
                    }else{
                        if (!mkdir($structure, 0777, true)) {
                            die('Failed to create directories...');
                        }
                    }
                    move_uploaded_file($file_tmp,"./data/userAvatars/" . $userID . "/" . $avatarName);
                    $this->db->changeUserAvatarImageName($userID, $avatarName);

                }else{
                    Flash::error($errors);
                    $this->redirect('administration/editUser?editedUserId=' . $userID);
                }
            }

            if($_POST['password'] != null){
                if($password == $_POST['password2']){
                    $this->db->changeUserPassword($userID, $password);
                }else{
                    Flash::error('Hesla se neshodují');
                    $this->redirect('administration/editUser?editedUserId=' . $userID);
                }
            }

            $this->db->editUserWithoutPassAvatar($userID, $username, $realName, $realSurname, $email, $tel, $role);

            Flash::success('Uživatel úspěšně upraven');
            $this->redirect(administration);


        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect(administration);
        }
    }

    public function FORM_editFineType(){
        if(isset($_POST['submitEditFineType'])){
            $fineId = $_POST['fineTypeId'];
            $fineName = $_POST['fineName'];
            $fineMoney = $_POST['money'];

            if($this->db->editFineType($fineId, $fineName, $fineMoney)){
                Flash::success('Typ pokuty úspěšně upraven');
                $this->redirect(administration);
            }else{
                Flash::success('Pokuta se nepodařila úspěšně upravit');
                $this->redirect(administration);
            }

        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect(administration);
        }
    }

    // -------------------- AKCE ------------------------------------------------

    /**
     * @param $id
     * Akce odstranuje uzivatele podle id
     */
    public function ACTION_deleteUser($id){
        $this->deleteDir('./data/userAvatars/' . $id);
        if($this->db->deleteUser($id)){
            Flash::success('Uživatel byl odstraněn');
        }else{
            Flash::error('Uživatele se nepodařilo odstanit');
        }
        $this->redirect(administration);
    }

    /**
     * @param $id
     * Akce poveruje uzivatele pokladnikem podle id
     */
    public function ACTION_entrustCashier($id){
        if($this->db->entrustUserToCashier($id)){
            Flash::warning('Uživatel byl pověřen pokladníkem');
        }else{
            Flash::error('Něco se pokazilo');
        }
        $this->redirect(administration);
    }

    /**
     * @param $id
     * Akce odpoveri uzivatele pokladnikem po id
     */
    public function ACTION_disableCashier($id){
        if($this->db->unEntrustUserToCashier($id)){
            Flash::warning('Uživatel byl odpověřen pokladníkem');
        }else{
            Flash::error('Něco se pokazilo');
        }
        $this->redirect(administration);
    }

    /**
     * @param $id
     * Akce odstranuje oddil podle id
     */
    public function ACTION_deleteDivision($id){
        if($this->db->deleteDivision($id)){
            Flash::success('Oddíl úspěšně ostraněn');
        }else{
            Flash::error('Oddíl nebyl odstraněn');
        }
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
     * Akce presmeruje stranku stranku na editaci typu pokuty
     */
    public function ACTION_redirectToEditFineType($id){
        $this->redirect(administration . "/editFineType?editedFineTypeId=" . $id);
    }

    /**
     * @param $id
     * Akce odstrani typ pokuty podle id
     */
    public function ACTION_deleteFineType($id){
        if($this->db->deleteFineType($id)){
            Flash::success("Typ pokuty byl odstraněn");
        }else{
            Flash::error("Něco se pokazilo");
        }
        $this->redirect(administration);
    }

}