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
    public function addDivision($nameDivision, $chief){
        $query = $this->pdo->prepare("INSERT INTO " . TABLE_DIVISIONS . " (nameDivision, chiefUserId) VALUES (:nameDivision, :chief)");

        $result = $query->execute(array(
            ":nameDivision" => $nameDivision,
            ":chief" => $chief,
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
    public function editDivision($idDivision, $nameDivision, $chief){
        $query = $this->pdo->prepare("UPDATE " . TABLE_DIVISIONS . " SET nameDivision=:nameDivision, chiefUserId=:chief  WHERE id=:idDivision");
        $result = $query->execute(array(
            "idDivision" => $idDivision,
            ":nameDivision" => $nameDivision,
            ":chief" => $chief,
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
            ":username" => $username,
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

    /**
     * @param array $users
     * @param array $fines
     * @param $cashierID
     * @return bool
     * Metoda pridava novou pokutu do db
     */
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

    /**
     * @return array|false
     * Metoda vraci vsechny pokuty
     */
    public function getAllFines(){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_FINER);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     * Metoda vraci vsechny pokuty se vsemi informacemi a jmeny
     */
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


            $userDivision = $user['idDivision'];

            if($userDivision != null){
                $currentDivision = $this->getDivisionById($user['idDivision']);
                $result[$j]['user']['hasDivision'] = true;
                $result[$j]['user']['division'] = $currentDivision['nameDivision'];

                $divisionFines = $this->getFinesAmountsByDivisionId($user['idDivision']);
                $division = $this->getDivisionById($user['idDivision']);
                $divisionChief = $this->getUserById($division['chiefUserId']);

                $result[$j]['division']['chiefName'] = $divisionChief['name'];
                $result[$j]['division']['chiefSurname'] = $divisionChief['surname'];
                $result[$j]['division']['chiefTel'] = $divisionChief['tel'];
                $result[$j]['division']['chiefEmail'] = $divisionChief['email'];

                $result[$j]['division']['unpaidAmount'] = $divisionFines[0];
                $result[$j]['division']['paidAmount'] = $divisionFines[1];
                $result[$j]['division']['allAmount'] = $divisionFines[2];
            }else{
                $result[$j]['user']['hasDivision'] = false;
            }

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
            if($currentUser['idDivision'] == null){
                $divisionName = "Nepřiřazeno";
            }else{
                $divisionName = $this->getDivisionById($currentUser['idDivision'])['nameDivision'];
            }

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

    /**
     * @param $divisionId
     * @return array|int[]
     * Metoda vraci castky oddílu podle id oddilu
     * [0] = nezaplacene pokuty
     * [1] = zaplacene pokuty
     * [2] = vsechny pokuty dohromady
     */
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

    /**
     * @param $fineId
     * @return bool
     * Metoda odstrani pokutu podle jeji id
     */
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

    /**
     * @param $idFine
     * @return bool
     * Metoda oznacuje pokutu jako zaplacenou podle jeji id
     */
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

    /**
     * @param $idFine
     * @return bool
     * Metoda oznacuje pokutu jako nezaplacenou podle jeji id
     */
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

    /**
     * @param $userID
     * @param $email
     * @return bool
     * Metoda upravuje email uzivatele podle jeho id
     */
    public function editUserEmailByUserId($userID, $email){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET email= :email WHERE id= :userId");
        $result = $query->execute(array(
            ":email" => $email,
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
     * @param $tel
     * @return bool
     * Metoda upravuje telefon uzivatele podle jeho id
     */
    public function editUserTelByUserId($userID, $tel){
        $query =  $this->pdo->prepare("UPDATE " . TABLE_USER . " SET tel= :tel WHERE id= :userId");
        $result = $query->execute(array(
            ":tel" => $tel,
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
     * @param $divisionId
     * @return array|false
     * Medota vraci zapasy podle id divize predanym v parametru
     */
    public function getMatchesByDivisionId($divisionId){
        $query =  $this->pdo->prepare("SELECT * FROM " . TABLE_MATCHES . " WHERE idDivision= :idDivision");
        $query->execute(array(
            ":idDivision" => $divisionId,
        ));

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $teamId
     * @return mixed
     * Metoda vraci nazev tymu podle jeho id predanym v parametru
     */
    public function getTeamNameById($teamId){
        $query =  $this->pdo->prepare("SELECT teamName FROM " . TABLE_TEAMS . " WHERE id= :teamId");
        $query->execute(array(
            ":teamId" => $teamId,
        ));

        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @param $divisionId
     * @return array|false
     * Metoda vraci zapasy podle id divize spolecne se jmenymy nazvy misto klicu
     */
    public function getMatchesByDivisionIdWithNames($divisionId){
        $allMatches = $this->getMatchesByDivisionId($divisionId);
        $j = 0;

        foreach ($allMatches as $match){
            $allMatches[$j]['homeTeamName'] = $this->getTeamNameById($match['idTeam1'])['teamName'];
            $allMatches[$j]['awayTeamName'] = $this->getTeamNameById($match['idTeam2'])['teamName'];
            $j++;
        }

        return $allMatches;
    }

    /**
     * @param $title
     * @param $content
     * @param $chiefId
     * @param $divisionId
     * @return bool
     * Metoda prida zpravu do databaze
     */
    public function addMessage($title, $content, $chiefId, $divisionId){
        $query = $this->pdo->prepare("INSERT INTO " . TABLE_MESSAGES . " (title, content, date, chiefId, divisionId) VALUES (:title, :content, :date, :chiefId, :divisionId)");

        date_default_timezone_set('Europe/Prague');
        $today = date("Y-m-d H:i:s");

        $result = $query->execute(array(
            ":title" => $title,
            ":content" => $content,
            ":date" => $today,
            ":chiefId" => $chiefId,
            ":divisionId" => $divisionId,
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
     * @return array
     * Metoda vradi pole se vsema zpravama. Jako klice prvniho stupne pole je id oddilu
     */
    public function getAllMessagesByDivisions(){
        $query = $this->pdo->prepare("SELECT * FROM " . TABLE_MESSAGES . " ORDER BY date DESC");
        $result = array();

        $query->execute();

        $messages = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($messages as $mess){
            $result[$mess['divisionId']][] = $mess;
        }

        return $result;
    }

    /**
     * @param $id
     * @return bool
     * Metoda odstrani zpravu z db podel jeji id
     */
    public function removeMessById($id){
        $query = $this->pdo->prepare("DELETE FROM " . TABLE_MESSAGES . " WHERE id = :idMess");
        $result = $query->execute(array(
            ":idMess" => $id,
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
     * @return array|false
     * Metoda vraci vsechny zpravy pro oddil identifikovany podle jeho id
     */
    public function getAllMessagesByIdDivision($idDivision){
        $query = $this->pdo->prepare("SELECT *  FROM " . TABLE_MESSAGES . " WHERE divisionId = :divisionId ORDER BY date DESC");
        $result = $query->execute(array(
            ":divisionId" => $idDivision,
        ));

        $allMess  = $query->fetchAll(PDO::FETCH_ASSOC);
        $j = 0;

        foreach ($allMess as $mess){
            $allMess[$j]['chief'] = $this->getUserById($mess['chiefId']);
            $j++;
        }

        return $allMess;
    }

    /**
     * @param $idMess
     * @return mixed
     * Metoda vraci jednu konkretni zpravu podle jeji id
     */
    public function getMessageById($idMess){
        $query = $this->pdo->prepare("SELECT *  FROM " . TABLE_MESSAGES . " WHERE id = :idMess");
        $query->execute(array(
            ":idMess" => $idMess,
        ));

        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

}

