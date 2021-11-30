<?php

class LoginController extends MainController
{
    public function zpracuj($parametry)
    {

        if (isset($_POST["username"]) && isset($_POST["password"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];

            $check = $this->checkLogin($username, $password);

            if($check != false){
                $_SESSION['name'] = $check['name'];
                $_SESSION['surname'] = $check['surname'];
                $_SESSION['userID'] = $check['id'];
                $_SESSION['userRole'] = $check['userRole'];
                $_SESSION['userDivision'] = $check['idDivision'];
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
                return array(
                    'name' => $user['name'],
                    'surname' => $user['surname'],
                    'id' => $user['id'],
                    'userRole' => $user['idRole'],
                    'idDivision' => $user['idDivision']
                );
            }
        }
        return false;
    }

}