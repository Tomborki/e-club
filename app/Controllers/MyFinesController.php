<?php


class MyFinesController extends MainController
{

    public function zpracuj($parametry)
    {

        $allMyFines = $this->db->getAllUserFinesWithNameAndMoney($_SESSION['userID']);
        $this->data['dept'] = $this->getDept()[0];
        $this->data['paid'] = $this->getDept()[1];
        $this->data['all'] = $this->getDept()[2];
        $unPaidFines  = array();
        $paidFines  = array();

        foreach ($allMyFines as $myFine){
            if($myFine['paid'] == 0){
                array_push($unPaidFines, $myFine);
            }elseif($myFine['paid'] == 1){
                array_push($paidFines, $myFine);
            }
        }

        $this->data['unPaidFines'] = $unPaidFines;
        $this->data['paidFines'] = $paidFines;

        $this->displayTwig();
    }

    /**
     * @return array|int[]
     * Metoda vraci dluh uzivatele
     * [0] = nezaplacene pokuty
     * [1] = zaplacene pokuty
     * [2] = vsechny poktuy dohromady
     */
    private function getDept(){
        $myfiners = $this->db->getAllFinesIdUser($_SESSION['userID']);
        $allUnpaidMoney = 0;
        $allPaidMoney = 0;
        $allFines = 0;

        foreach ($myfiners as $finer){
            $money = $this->db->getFinerInfo($finer['typeFines_id'])['money'];

            if($finer['paid'] == 0){
                $allUnpaidMoney = $allUnpaidMoney + $money;
            }
            if($finer['paid'] == 1){
                $allPaidMoney = $allPaidMoney + $money;
            }
            $allFines = $allFines + $money;
        }

        return array($allUnpaidMoney, $allPaidMoney, $allFines);
    }

}