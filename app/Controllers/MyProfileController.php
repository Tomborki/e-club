<?php

use \Tamtamchik\SimpleFlash\Flash;

class MyProfileController extends MainController
{

    public function zpracuj($parametry)
    {
        $this->data['currentUser'] = $this->db->getUserById($_SESSION['userID']);

        $this->displayTwig();
    }

    /**
     * Funkce zpracovava odpovedi od formulare editEmail
     * Formular na upravovani emailu uzivatele
     */
    public function FORM_editEmail(){
        if(isset($_POST['submitEditEmailForm'])){
            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

            if($this->db->editUserEmailByUserId($_SESSION['userID'], $email)){
                Flash::success('Email byl upraven');
                $this->redirect('my-profile');
            }else{
                Flash::error('Email se nepovedlo upravit');
                $this->redirect('my-profile');
            }
        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect('my-profile');
        }
    }

    /**
     * Funkce zpracovava odpovedi od formulare editTel
     * Formular na upravovani telefonu uzivatele
     */
    public function FORM_editTel(){
        if(isset($_POST['submitEditTelForm'])){
            $tel = $_POST['tel'];

            if($this->db->editUserTelByUserId($_SESSION['userID'], $tel)){
                Flash::success('Telefon byl upraven');
                $this->redirect('my-profile');
            }else{
                Flash::error('Telefon se nepovedlo upravit');
                $this->redirect('my-profile');
            }
        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect('my-profile');
        }
    }

    /**
     * Funkce zpracovava odpovedi od formulare editAvatar
     * Formular na upravovani avataru uzivatele
     */
    public function FORM_editAvatar(){
        if(isset($_POST['submitEditAvatarForm'])){
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
                    $structure = './data/userAvatars/' . $_SESSION['userID'] . '/';
                    if(is_dir($structure)){
                        $this->deleteFiles($structure);
                    }else{
                        if (!mkdir($structure, 0777, true)) {
                            die('Failed to create directories...');
                        }
                    }
                    move_uploaded_file($file_tmp,"./data/userAvatars/" . $_SESSION['userID'] . "/" . $avatarName);
                    $this->db->changeUserAvatarImageName($_SESSION['userID'], $avatarName);
                    Flash::success('Avatar byl upraven');
                    $_SESSION['userAvatar'] = $avatarName;
                    $this->redirect('my-profile');

                }else{
                    Flash::error($errors);
                    $this->redirect('my-profile');
                }
            }
        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect('my-profile');
        }
    }

    /**
     * Funkce zpracovava odpovedi od formulare editPass
     * Formular na upravovani hesla uzivatele
     */
    public function FORM_editPass(){
        if(isset($_POST['submitEditPassForm'])){
            $oldPass = htmlspecialchars($_POST['oldPass'], ENT_QUOTES, 'UTF-8');
            $newPass = htmlspecialchars($_POST['newPass'], ENT_QUOTES, 'UTF-8');
            $newPass2 = htmlspecialchars($_POST['newPass2'], ENT_QUOTES, 'UTF-8');

            $currentUserPassHash = $this->db->getUserById($_SESSION['userID'])['password'];

            if(!password_verify($oldPass, $currentUserPassHash)){
                Flash::error('Staré heslo nebylo správné');
                Flash::error('Heslo se nepovedlo změnit');
                $this->redirect('my-profile');
            }

            if($newPass != $newPass2){
                Flash::error('Nové hesla se neshodují');
                Flash::error('Heslo se nepovedlo změnit');
                $this->redirect('my-profile');
            }

            if($this->db->changeUserPassword($_SESSION['userID'], $newPass)){
                Flash::success('Heslo bylo změněno');
                $this->redirect('my-profile');
            }else{
                Flash::error('Heslo se nepovedlo změnit');
                $this->redirect('my-profile');
            }
        }else{
            Flash::error('Někde se stala chyba');
            $this->redirect('my-profile');
        }
    }

}