<?php


class UsersController extends MainController
{

    public function zpracuj($parametry)
    {

        $allUsers = $this->db->getAllUsers();
        $j = 0;

        foreach ($allUsers as $user){
            if($this->db->getDivisionById($user['idDivision']) == null){
                $allUsers[$j]["nameDivision"] = 'Nepřiřazeno';
            }else{
                $allUsers[$j]["nameDivision"] = $this->db->getDivisionById($user['idDivision'])['nameDivision'];
            }
            $j++;
        }
        
        $this->data['allUsers'] = $allUsers;

        $this->displayTwig();
    }

}