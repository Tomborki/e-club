<?php

use \Tamtamchik\SimpleFlash\Flash;

class CashierPageController extends MainController
{
    public function zpracuj($parametry)
    {
        $this->data['usersInDivisions'] = $this->usersInDivisions();
        $this->data['fineTypes'] = $this->db->getAllTypeFines();
        $this->data['allFines'] = $this->db->getAllFinesWithNames();

        $this->displayTwig();
    }

    /**
     * Funkce zpracovava odpovedi od formulare addFine
     * Formular na pridavani pokut
     */
    public function FORM_addFine(){
        if(isset($_POST['submitAddFineToUsersForm'])){
            $users = $_POST['selectedUsers'];
            $fines = $_POST['selectedFines'];

            if($this->db->addFine($users, $fines, $_SESSION['userID'])){
                Flash::success('Pokuta/pokuty byly úspěšně přidány');
            }else{
                Flash::error('Pokuta/pokuty se nepovedli přidat');
            }

            $this->redirect('cashier-page');
        }else{
            $this->redirect('cashier-page');
        }
    }

    /**
     * @return array
     * Metoda vraci pole s uzivateli v jednotlivých oddílech
     */
    public function usersInDivisions(){
        $allUsers = $this->db->getAllUsers();
        $result = array();

        foreach ($allUsers as $user){

            $division = $this->db->getDivisionById($user['idDivision']);

            if($division == null){
                $division['nameDivision'] = "Nepřiřazeno";
            }

            $result[$division['nameDivision']][] = $user;
        }

        return $result;
    }

    /**
     * @param $idFine
     * Akce odstranuje pokutu podle jeji id
     */
    public function ACTION_deleteFine($idFine){
        if($this->db->deletFineById($idFine)){
            Flash::success('Pokuta byla úspěšně odpuštěna');
        }else{
            Flash::error('Pokutu se nepodařilo odpustit');
        }

        $this->redirect('cashier-page');
    }

    /**
     * @param $idFine
     * Akce oznaci pokutu jako zaplacenou podle jeji id
     */
    public function ACTION_markPaidFine($idFine){
        if($this->db->editFinePaidToTrue($idFine)){
            Flash::success('Pokuta byla úspěšně označena jako <b>zaplacená</b>');
        }else{
            Flash::error('Pokutu se nepodařilo označit jako zaplacenou');
        }

        $this->redirect('cashier-page');
    }

    /**
     * @param $idFine
     * Akce oznaci pokutu jako nezaplacenou podle jeji id
     */
    public function ACTION_markUnpaidFine($idFine){
        if($this->db->editFinePaidToFalse($idFine)){
            Flash::success('Pokuta byla úspěšně označena jako <b>nezaplacená</b>');
        }else{
            Flash::error('Pokutu se nepodařilo označit jako zaplacenou');
        }

        $this->redirect('cashier-page');
    }

}