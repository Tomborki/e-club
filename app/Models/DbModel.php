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
     * @return array
     * Metoda vrati vsechny uzivatele v db
     */
    public function getUserById($id):array {
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_USER . " WHERE id= :idUser");
        $query->execute(array(
            "idUser" => $id
        ));
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @param $username
     * @param $password
     * @param $name
     * @param $surname
     * @param $email
     * @param $tel
     * @param null $idRole
     * @param null $idDivision
     * @param $cashier
     * @return false|string
     * Metoda prida noveho uzivatele do databaze. Samotna metoda se stara o zaheshovani hesla
     */
    public function addUser($username, $password, $name, $surname, $email, $tel, $avatar, $idRole = NULL, $idDivision = NULL, $cashier = 0){
        $query = $this->pdo->prepare("INSERT INTO " . TABLE_USER . " (username, password, name, surname, email, tel, idRole, idDivision, cashier, avatarImageName) 
                                    VALUES (:username, :password, :realName, :surname, :email, :tel, :idRole, :idDivision, :cashier, :avatar)");
        if($idRole == null){
            $idRole = 3;
        }


        $result = $query->execute(array(
            ":username" => $username,
            ":password" => password_hash($password, PASSWORD_DEFAULT),
            ":realName" => $name,
            ":surname" => $surname,
            ":email" => $email,
            ":tel" => $tel,
            ":idRole" => $idRole,
            ":idDivision" => $idDivision,
            ":cashier" => $cashier,
            ":avatar" => $avatar
        ));

        print_r($query->errorInfo());

        if ($result) {
            // neni false

            return $this->pdo->lastInsertId();
        } else {
            // je false
            echo $result;
            return false;
        }
    }

    public function entrustUserToCashier($userId){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET cashier= 1  WHERE id= :userId");
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

    public function unEntrustUserToCashier($userId){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET cashier= 0  WHERE id= :userId");
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
     * @return bool
     * Metoda maze uzivatele podle zadaneho id. Pokud se prikaz nevykoda dobre, metoda vrati false... jinak true.
     */
    public function deleteUser(int $userId):bool {
        $query = $this->pdo->prepare("DELETE FROM " . TABLE_USER . " WHERE id = :userId");
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

    /**
     * @param $nameDivision
     * @param $chief
     * @param $telContact
     * @param $emailContact
     * @return bool
     * Metoda pridava novy oddil. Pokud se prikaz nevykoda dobre, metoda vrati false... jinak true.
     */
    public function addDivision($nameDivision, $chief, $telContact = NULL, $emailContact = NULL){
        $query = $this->pdo->prepare("INSERT INTO " . TABLE_DIVISIONS . " (nameDivision, chief, telContact, emailContact) VALUES (:nameDivision, :chief, :telContact, :emailContact)");

        $result = $query->execute(array(
            ":nameDivision" => $nameDivision,
            ":chief" => $chief,
            ":telContact" => $telContact,
            ":emailContact" => $emailContact
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
     * @param $idDivision
     * @return bool
     * Metoda odstrani oddil podle jeho id
     */
    public function deleteDivision($idDivision){
        $query = $this->pdo->prepare("DELETE FROM " . TABLE_DIVISIONS . " WHERE id = :divisionId");
        $result = $query->execute(array(
            ":divisionId" => $idDivision,
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
     * @param $idDivision
     * @return mixed
     * Metoda vrati oddil podle jejiho id
     */
    public function getDivisionById($idDivision){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_DIVISIONS . " WHERE id = :divisionId");
        $query->execute(array(
            ":divisionId" => $idDivision,
        ));

        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @param $idDivision
     * @return mixed
     * Metoda vrati oddil podle jejiho id
     */
    public function editDivision($idDivision, $nameDivision, $chief, $telContact = NULL, $emailContact = NULL){
        $query = $this->pdo->prepare("UPDATE " . TABLE_DIVISIONS . " SET nameDivision=:nameDivision, chief=:chief, telContact=:telContact, emailContact=:emailContact  WHERE id=:idDivision");
        $result = $query->execute(array(
            "idDivision" => $idDivision,
            ":nameDivision" => $nameDivision,
            ":chief" => $chief,
            ":telContact" => $telContact,
            ":emailContact" => $emailContact
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

    public function getRoleById($roleId){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_ROLES . " WHERE id = :roleId");
        $query->execute(array(
            "roleId" => $roleId
        ));
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    public function getAllRoles(){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_ROLES);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTypeFines(){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_TYPE_FINES);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addFineType($nameFine, $money){
        $query = $this->pdo->prepare("INSERT INTO " . TABLE_TYPE_FINES . " (nameFine, money) VALUES (:nameFine, :money)");
        $result = $query->execute(array(
            "nameFine" => $nameFine,
            ":money" => $money,
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

    public function deleteFineType($idFine){
        $query = $this->pdo->prepare("DELETE FROM " . TABLE_TYPE_FINES . " WHERE id = :fineID");
        $result = $query->execute(array(
            ":fineID" => $idFine,
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

    public function changeUserPassword($userId, $newPassword){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET password= :password  WHERE id= :userId");
        $result = $query->execute(array(
            ":password" => password_hash($newPassword, PASSWORD_DEFAULT),
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

    public function changeUserAvatarImageName($userId, $newName){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET avatarImageName= :avatarImageName  WHERE id= :userId");
        $result = $query->execute(array(
            ":avatarImageName" => $newName,
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

    public function editUserWithoutPassAvatar($userId, $username, $name, $surname, $email, $tel, $idRole = NULL){
        $query = $this->pdo->prepare("UPDATE " . TABLE_USER . " SET username=:username, name=:name, surname=:surname, email=:email, tel=:tel, idRole=:idRole  WHERE id= :userId");
        $result = $query->execute(array(
            "username" => $username,
            ":name" => $name,
            ":surname" => $surname,
            ":email" => $email,
            ":tel" => $tel,
            ":idRole" => $idRole,
            ":userId" => $userId,
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

}