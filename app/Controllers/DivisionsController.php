<?php


class DivisionsController extends MainController
{

    public function zpracuj($parametry)
    {
        $this->data['divisions'] = $this->db->getAllDivisions();

        if (isset($_GET["action"]))
        {
            $func = $_GET['action'];
            $this->$func($_GET['id']);
            $this->redirect(divisions);
        }

        $this->displayTwig();
    }

    private function likeDivision($id){
        $this->db->userLikedDivision($_SESSION['userID'], $id);
        $_SESSION['userDivision'] = $id;
    }

}