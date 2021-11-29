<?php


class HomeController extends MainController
{

    public function zpracuj($parametry)
    {
        $this->data['name'] = $_SESSION['name'];
        $this->data['dept'] = $this->getDept();

        $this->displayTwig();
    }

    private function getDept(){
        $myfiners = $this->db->getAllFinesIdUser($_SESSION['userID']);
        $allMoney = 0;

        foreach ($myfiners as $finer){
            if($finer['paid'] == 0){
                $allMoney = $allMoney + $this->db->getFinerInfo($finer['typeFines_id'])['money'];
            }
        }

        return $allMoney;
    }

}