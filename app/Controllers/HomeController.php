<?php


class HomeController extends MainController
{

    public function zpracuj($parametry)
    {
        $this->data['name'] = $_SESSION['name'];
        $this->data['dept'] = $this->getDept();
        $this->data['userDivision'] = $_SESSION['userDivision'];
        $this->data['currentMessages'] = $this->db->getAllMessagesByIdDivision($_SESSION['userDivision']);

        if($_SESSION['userDivision'] != null){
            $allMatches = $this->db->getMatchesByDivisionIdWithNames($_SESSION['userDivision']);
            $unPlayed = array();
            $played = array();

            foreach ($allMatches as $match) {
                if ($match['end'] == 0) {
                    $unPlayed[] = $match;
                } else {
                    $played[] = $match;
                }
            }

            $this->data['unplayedMatches'] = $unPlayed;
            $this->data['playedMatches'] = $played;
        }

        $this->displayTwig();
    }

    /**
     * @return int|mixed
     * Metoda vraci dluh uzivatele
     */
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