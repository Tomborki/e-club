<?php


class DbModel {

    /** @var PDO $pdo  Objekt pracujici s databazi prostrednictvim PDO. */
    private $pdo;

    /**
     * Inicializace pripojeni k databazi.
     */
    public function __construct() {
        // inicializace DB
        $this->pdo = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME, DB_USER, DB_PASS);
        // vynuceni kodovani UTF-8
        $this->pdo->exec("set names utf8");
    }

    /**
     *  Vrati seznam vsech uzivatelu pro spravu uzivatelu.
     *  @return array Obsah spravy uzivatelu.
     */
    public function getAllUsers():array {
        // pripravim dotaz
        $q = "SELECT * FROM ".TABLE_USER;
        // provedu a vysledek vratim jako pole
        // protoze je o uzkazku, tak netestuju, ze bylo neco vraceno
        return $this->pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);
        return $test;
    }

    /**
     *  Vrati seznam vsech uzivatelu pro spravu uzivatelu.
     *
     */
    public function addUser($username, $password){
        // pripravim dotaz
        $q = "INSERT INTO " . TABLE_USER . " (username, password, idRole) VALUES ('" . $username . "','" . password_hash($password, PASSWORD_DEFAULT) . "',1)";
        // provedu a vysledek vratim jako pole
        // protoze je o uzkazku, tak netestuju, ze bylo neco vraceno
        $res = $this->pdo->query($q);
    }

    /**
     *  Smaze daneho uzivatele z DB.
     *  @param int $userId  ID uzivatele.
     */
    public function deleteUser(int $userId):bool {
        // pripravim dotaz
        $q = "DELETE FROM ".TABLE_USER." WHERE id_user = $userId";
        // provedu dotaz
        $res = $this->pdo->query($q);
        // pokud neni false, tak vratim vysledek, jinak null
        if ($res) {
            // neni false
            return true;
        } else {
            // je false
            return false;
        }
    }

    /**
     * @param int $userId
     * @return array|false
     * Metoda vrati vsechny pokuty uzivatele. At uz zaplacené nebo nezaplacene
     */
    public function getAllFinesIdUser(int $userId){
        // pripravim dotaz
        $q = "SELECT * FROM ". TABLE_FINER . " WHERE users_id = " . $userId;
        // provedu a vysledek vratim jako pole
        // protoze je o uzkazku, tak netestuju, ze bylo neco vraceno
        return $this->pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $userId
     * @return array|false
     * Metoda vrati vsechny pokuty uzivatele. At uz zaplacené nebo nezaplacene
     */
    public function getFinerInfo(int $finerID){
        // pripravim dotaz
        $q = "SELECT * FROM ". TABLE_TYPE_FINES . " WHERE id = " . $finerID;
        // provedu a vysledek vratim jako pole
        // protoze je o uzkazku, tak netestuju, ze bylo neco vraceno
        return $this->pdo->query($q)->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @param int $userId
     * @return array|false
     * Metoda vrati vsechny pokuty uzivatele. At uz zaplacené nebo nezaplacene
     */
    public function getAllDivisions(){
        // pripravim dotaz
        $q = "SELECT * FROM ". TABLE_DIVISIONS;
        // provedu a vysledek vratim jako pole
        return $this->pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $userId
     * @return array|false
     * Metoda vrati vsechny pokuty uzivatele. At uz zaplacené nebo nezaplacene
     */
    public function userLikedDivision($userID, $divisionID){
        // pripravim dotaz
        $q = "UPDATE " . TABLE_USER . " SET idDivision='" . $divisionID . "' WHERE id=" . $userID;
        // provedu a vysledek vratim jako pole
        $res = $this->pdo->query($q);
    }

    /**
     * @param int $userId
     * @return array|false
     * Metoda vrati vsechny pokuty uzivatele. At uz zaplacené nebo nezaplacene
     */
    public function unLikeDivision($userID){
        // pripravim dotaz
        $q = "UPDATE " . TABLE_USER . " SET idDivision=NULL WHERE id=" . $userID;
        // provedu a vysledek vratim jako pole
        $res = $this->pdo->query($q);
    }

}