<?php

class LoginController extends MainController
{
    public function zpracuj($parametry)
    {

        if (isset($_POST["username"]) && isset($_POST["password"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];

            if($this->checkLogin($username, $password)){
                $this->data['errorMessage'] = "Ověřeno";
            }else{
                $this->data['errorMessage'] = "Nesprávné přihlašovací jméno nebo heslo";
            }
        }

        $this->displayTwig();
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     * Metoda overuje uzivatelske jmeno a heslo z databze
     */
    private function checkLogin($username, $password):bool{
        foreach ($this->db->getAllUsers() as $user){
            if($user['username'] == $username && password_verify($password, $user['password'])){
                return true;
            }
        }
        return false;
    }

}