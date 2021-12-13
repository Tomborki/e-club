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
     * @return array
     * Metoda vrati vsechny uzivatele v db
     */
    public function getAllUsers():array {
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_USER);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $username
     * @param $password
     * @param null $idRole
     * @return bool
     * Metoda pridava noveho uzivatele. Heslo se rovnou zahesuje. Pokud se prikaz nevykoda dobre, metoda vrati false... jinak true.
     */
    public function addUser($username, $password, $idRole = null){
        $query = $this->pdo->prepare("INSERT INTO " . TABLE_USER . " (username, password, idRole) VALUES (:username, :password, :idRole)");

        if($idRole == null){
            $idRole = 3;
        }

        $result = $query->execute(array(
            ":username" => $username,
            ":password" => password_hash($password, PASSWORD_DEFAULT),
            ":idRole" => $idRole
        ));

        if ($result) {
            // neni false
            return true;
        } else {
            // je false
            return false;
        }
    }

    /**
     * @param int $userId
     * @return bool
     * Metoda maze uzivatele podle zadaneho id. Pokud se prikaz nevykoda dobre, metoda vrati false... jinak true.
     */
    public function deleteUser(int $userId):bool {
        $query = $this->pdo->prepare("DELETE FROM " . TABLE_USER . " WHERE id_user = :userId");
        $result = $query->execute(array(
                    ":userId" => $userId
                ));

        // pokud neni false, tak vratim vysledek, jinak null
        if ($result) {
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
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_FINER  . " WHERE users_id = :userID");
        $query->execute(array(
            ":userID" => $userId
        ));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $finerID
     * @return mixed
     * Metoda vraci jmeno pokuty podle jeho id
     */
    public function getFinerInfo(int $finerID){
        $query =  $this->pdo->prepare("SELECT * FROM ". TABLE_TYPE_FINES . " WHERE id = :finerID");
        $query->execute(array(
            ":finerID" => $finerID
        ));
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @return array|false
     * Metoda vraci vsechny oddily
     */
    public function getAllDivisions(){
        $query =  $this->pdo->prepare("SELECT * FROM ". TABLE_DIVISIONS);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $userID
     * @param $divisionID
     * Metoda upravuje oblibeny oddil u uzivatele v db
     */
    public function userLikedDivision($userID, $divisionID){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET idDivision= :idDivision  WHERE id= :userId");
        $query->execute(array(
            ":idDivision" => $divisionID,
            ":userId" => $userID
        ));
    }

    /**
     * @param $userID
     * Metoda priradi hodnotu null u oblibeneho oddilu u uzivatele podle jeho id v db
     */
    public function unLikeDivision($userID){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET idDivision=NULL WHERE id= :userId");
        $query->execute(array(
            ":userId" => $userID
        ));
    }

}