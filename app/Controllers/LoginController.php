<?php

class LoginController extends MainController
{
    public function zpracuj($parametry)
    {

        if (isset($_POST["username"]) && isset($_POST["password"]))
        {
            $username = htmlspecialchars($_POST["username"], ENT_QUOTES, 'UTF-8');
            $password = htmlspecialchars($_POST["password"], ENT_QUOTES, 'UTF-8');

            $check = $this->checkLogin($username, $password);

            if($check != false){
                $_SESSION['name'] = $check['name'];
                $_SESSION['surname'] = $check['surname'];
                $_SESSION['userID'] = $check['id'];
                $_SESSION['userRole'] = $check['idRole'];
                $_SESSION['userDivision'] = $check['idDivision'];
                $_SESSION['userAvatar'] = $check['avatarImageName'];
                $_SESSION['cashier'] = $check['cashier'];
                $this->redirect(home);
            }else {
                $this->data['errorMessage'] = "Nesprávné přihlašovací jméno nebo heslo";
            }
        }else{
            session_destroy();
        }

        $this->displayTwig();
    }

    /**
     * @param $username
     * @param $password
     * Metoda overuje uzivatelske jmeno a heslo z databze
     */
    private function checkLogin($username, $password){
        foreach ($this->db->getAllUsers() as $user){
            if($user['username'] == $username && password_verify($password, $user['password'])){
                return $user;
            }
        }
        return false;
    }

}