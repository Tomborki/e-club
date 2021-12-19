<?php


class MyMatchesController extends MainController
{

    public function zpracuj($parametry)
    {

        $this->data['userDivision'] = $_SESSION['userDivision'];

        if($_SESSION['userDivision'] != null){
            $allMatches = $this->db->getMatchesByDivisionIdWithNames($_SESSION['userDivision']);
            $unPlayed = array();
            $played = array();

            foreach ($allMatches as $match){
                if($match['end'] == 0){
                    $unPlayed[] = $match;
                }else{
                    $played[] = $match;
                }
            }

            $this->data['unplayedMatches'] = $unPlayed;
            $this->data['playedMatches'] = $played;
        }


        $this->displayTwig();
    }

}