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
     * @param $avatar
     * @param null $idRole
     * @param null $idDivision
     * @param int $cashier
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

    /**
     * @param $userId
     * @return bool
     * Metoda poveri uzivatele pokladnikem podle jeho id
     */
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

    /**
     * @param $userId
     * @return bool
     * Metoda odpoveri uzivatele pokladnikem podle jeho id
     */
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
     * @param $idUser
     * @return array|false
     * Metoda vraci vsechny pokuty ktere patri uzivatele s jeho predanym id. Vraci se i nazev a hodnota
     */
    public function getAllUserFinesWithNameAndMoney($idUser){
        $allFines = $this->getAllFinesIdUser($idUser);
        $j = 0;

        foreach ($allFines as $oneFine){
            $fineInfo = $this->getFinerInfo($oneFine['typeFines_id']);
            $allFines[$j]['fineName'] = $fineInfo['nameFine'];
            $allFines[$j]['money'] = $fineInfo['money'];
            $j++;
        }

        return $allFines;
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
        $result = $query->execute(array(
            ":idDivision" => $divisionID,
            ":userId" => $userID
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
     * @param $userID
     * Metoda priradi hodnotu null u oblibeneho oddilu u uzivatele podle jeho id v db
     */
    public function unLikeDivision($userID){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET idDivision=NULL WHERE id= :userId");
        $result = $query->execute(array(
            ":userId" => $userID
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
        if($idDivision == null){
            return null;
        }

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

    /**
     * @param $roleId
     * @return mixed
     * Metoda vrati roly podle jeji id
     */
    public function getRoleById($roleId){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_ROLES . " WHERE id = :roleId");
        $query->execute(array(
            "roleId" => $roleId
        ));
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @return array|false
     * Metoda vrati vsechny role
     */
    public function getAllRoles(){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_ROLES);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array|false
     * Metoda vraci vsechny typy pokut
     */
    public function getAllTypeFines(){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_TYPE_FINES);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $nameFine
     * @param $money
     * @return bool
     * Medota prida novy typ pokuty
     */
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

    /**
     * @param $idFine
     * @return bool
     * Metoda odstrani typ pokuty podle id
     */
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

    /**
     * @param $userId
     * @param $newPassword
     * @return bool
     * Metoda zmeni uzivateli jeho heslo. Uzivatel se identifikuje podle jeho id. Heslo automaticky zahesovano
     */
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

    /**
     * @param $userId
     * @param $newName
     * @return bool
     * Metoda upravi jmeno obrazku, ktery smeruje na avatar v ulozisti
     */
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

    /**
     * @param $userId
     * @param $username
     * @param $name
     * @param $surname
     * @param $email
     * @param $tel
     * @param null $idRole
     * @return bool
     * Metoda upravi utivatele v databazi podle jeho id. V teto metode se nezadava heslo ani jmeno obrazku avatara
     */
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

    /**
     * @param $idFine
     * @return mixed
     * Metoda vrati typ pokuty podle jeho id
     */
    public function getFineTypeById($idFine){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_TYPE_FINES . " WHERE id = :idFine");
        $query->execute(array(
            "idFine" => $idFine
        ));
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @param $idFine
     * @param $fineName
     * @param $fineMoney
     * @return bool
     * Metoda upravi typo pokuty
     */
    public function editFineType($idFine, $fineName, $fineMoney){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_TYPE_FINES . " SET nameFine= :nameFine, money= :money  WHERE id= :fineId");
        $result = $query->execute(array(
            ":fineId" => $idFine,
            ":nameFine" => $fineName,
            ":money" => $fineMoney,
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

    public function addFine(array $users, array $fines, $cashierID){
        date_default_timezone_set('Europe/Prague');
        $today = date("Y-m-d H:i:s");
        $query =  $this->pdo->prepare("INSERT INTO " . TABLE_FINER . " (typeFines_id, users_id, date, paid, cashierId) VALUES (:typeFines_id, :users_id, :date, :paid, :cashierId)");

        foreach ($users as $user){
            foreach ($fines as $fine){
                $result = $query->execute(array(
                    ":typeFines_id" => $fine,
                    ":users_id" => $user,
                    ":date" => $today,
                    ":paid" => 0,
                    ":cashierId" => $cashierID
                ));
                if(!$result){
                    return false;
                }
            }
        }

        return true;
    }

    public function getAllFines(){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_FINER);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllFinesWithNames(){
        $fines = $this->getAllFines();
        $result = array();
        $j = 0;

        foreach ($fines as $fine){
            $result[$j]['typeFine'] = $this->getFineTypeById($fine['typeFines_id']);

            $result[$j]['id'] = $fine['id'];
            $user = $this->getUserById($fine['users_id']);
            $result[$j]['user']['id'] = $user['id'];
            $result[$j]['user']['name'] = $user['name'];
            $result[$j]['user']['surname'] = $user['surname'];
            $result[$j]['user']['tel'] = $user['tel'];
            $result[$j]['user']['email'] = $user['email'];
            $result[$j]['user']['paidAmount'] = $this->getFinesAmountsByUserId($user['id'])[1];
            $result[$j]['user']['unpaidAmount'] = $this->getFinesAmountsByUserId($user['id'])[0];
            $result[$j]['user']['allAmount'] = $this->getFinesAmountsByUserId($user['id'])[2];

            $currentDivison = $this->getDivisionById($user['idDivision']);
            $result[$j]['user']['division'] = $currentDivison['nameDivision'];
            $result[$j]['user']['divisionChief'] = $currentDivison['chief'];
            $result[$j]['user']['divisionChiefTel'] = $currentDivison['telContact'];
            $result[$j]['user']['divisionChiefEmail'] = $currentDivison['emailContact'];


            $divisionFines = $this->getFinesAmountsByDivisionId($user['idDivision']);
            $result[$j]['division']['unpaidAmount'] = $divisionFines[0];
            $result[$j]['division']['paidAmount'] = $divisionFines[1];
            $result[$j]['division']['allAmount'] = $divisionFines[2];


            $result[$j]['date'] = $fine['date'];
            $result[$j]['paid'] = $fine['paid'];

            $cashier = $this->getUserById($fine['cashierId']);
            $result[$j]['cashier']['id'] = $cashier['id'];
            $result[$j]['cashier']['name'] = $cashier['name'];
            $result[$j]['cashier']['surname'] = $cashier['surname'];
            $j++;
        }

        return $result;
    }

    /**
     * @param $userId
     * @return array|int[]
     * Metoda vraci neuhrazene, uhrazene a vsechny pokuty dohromady
     * [0] = neuhrazene pokuty
     * [1] = uhrazene pokuty
     * [2] = vsechny pokuty
     */
    private function getFinesAmountsByUserId($userId){
        $myfiners = $this->getAllFinesIdUser($userId);
        $allUnpaidMoney = 0;
        $allPaidMoney = 0;
        $allFines = 0;

        foreach ($myfiners as $finer){
            $money = $this->getFinerInfo($finer['typeFines_id'])['money'];

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

    /**
     * @return array
     * Metoda vraci pole pokut podle jednotlivých oddilu
     */
    public function getDivisionFines(){
        $fines = $this->getAllFines();
        $result = array();
        $j = 0;

        foreach ($fines as $fine){
            $currentUser = $this->getUserById($fine['users_id']);
            $currentTypeFine = $this->getFineTypeById($fine['typeFines_id']);
            $divisionName = $this->getDivisionById($currentUser['idDivision'])['nameDivision'];
            $fine['currentUser'] = $currentUser;
            $fine['currentFineType'] = $currentTypeFine;
            $result[$divisionName][] = $fine;
        }

        return $result;
    }

    /**
     * @param $divisionId
     * @return mixed
     * Metoda vraci pole pokut pouze pro konkretni oddil. Oddil se identifikuje podle jeho id predanym v parametru metody
     */
    public function getDivisionFinesByDivisionId($divisionId){
        $allDivisionFines = $this->getDivisionFines();
        $correctDivisionName = $this->getDivisionById($divisionId)['nameDivision'];

        return($allDivisionFines[$correctDivisionName]);
    }


    public function getFinesAmountsByDivisionId($divisionId){
        $currentDivisionFines = $this->getDivisionFinesByDivisionId($divisionId);
        $allUnpaidMoney = 0;
        $allPaidMoney = 0;
        $allFines = 0;

        foreach ($currentDivisionFines as $fine){
            $money = $fine['currentFineType']['money'];

            if($fine['paid'] == 0){
                $allUnpaidMoney = $allUnpaidMoney + $money;
            }
            if($fine['paid'] == 1){
                $allPaidMoney = $allPaidMoney + $money;
            }
            $allFines = $allFines + $money;
        }

        return array($allUnpaidMoney, $allPaidMoney, $allFines);
    }

    public function deletFineById($fineId){
        $query = $this->pdo->prepare("DELETE FROM " . TABLE_FINER . " WHERE id = :fineID");
        $result = $query->execute(array(
            ":fineID" => $fineId,
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

    public function editFinePaidToTrue($idFine){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_FINER . " SET paid= :paid WHERE id= :fineId");
        $result = $query->execute(array(
            ":fineId" => $idFine,
            ":paid" => 1
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

    public function editFinePaidToFalse($idFine){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_FINER . " SET paid= :paid WHERE id= :fineId");
        $result = $query->execute(array(
            ":fineId" => $idFine,
            ":paid" => 0
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

