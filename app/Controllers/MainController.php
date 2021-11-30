<?php

abstract class MainController
{
    protected $data = array();
    protected $view = "";
    protected $header = array('titulek' => '', 'klicova_slova' => '', 'popis' => '');
    protected $twig;
    protected $db;
    protected $blackListRoles;

    public function __construct()
    {
        $this->loadTwig();
        $this->db = new DbModel();
        $this->checkUserLogin(get_called_class());
    }

    abstract function zpracuj($parametry);


    public function showView()
    {
        if ($this->view)
        {
            //extract($this->data);
            require('../' . DIRECTORY_VIEWS . $this->view . ".html.twig");
        }
    }

    /**
     * Funkce presmerovava stranku na stranku zadanou v parametru
     * @param $url
     */
    public function redirect($url)
    {
        if (headers_sent()){
            die('<script type="text/javascript">window.location=\''.$url.'\';</script‌​>');
        }else{
            header('Location: ' . $url);
            die();
        }
    }

    /**
     * Funkce vypisuje data do prislusne sablony. Zaroven overuje, zda je uzivatel prihlaseny
     */
    public function displayTwig($specificController = NULL){

        // ----------------- Check login ------------------
        $this->checkUserLogin(get_called_class());


        $controllerName = strtolower(str_replace('Controller', '', get_called_class()));

        //Predani zakladnich informaci o strance
        if(isset($_SESSION['name']) && isset($_SESSION['surname'])){
            $this->data['name'] = $_SESSION['name'];
            $this->data['surname'] = $_SESSION['surname'];
            $this->data['userID'] = $_SESSION['userID'];
            $this->data['userRole'] = $_SESSION['userRole'];
        }

        $this->data['navItems'] = NAV_ITEMS;
        $this->data['pageName'] = $controllerName;
        $this->data['mainColor'] = MAIN_APP_COLOR;

        if(!($this->checkRole(get_called_class(), $this->getAllowRoles(get_called_class())))){
            $this->twig->display('opravneni.html.twig', $this->data);
            return;
        }

        if(is_null($specificController)){
            $this->twig->display($controllerName . '.html.twig', $this->data);
        }else{
            $this->twig->display($specificController . '.html.twig', $this->data);
        }

    }

    /**
     * Funkce nacita samostatny twig
     */
    function loadTwig(){

        $loader = new Twig\Loader\FilesystemLoader($this->loadAllTemplates());
        // set up environment
        $params = array(
            'debug' => DEBUG_MODE,
            'cache' => "../cache",
            'auto_reload' => true, // disable cache
            'autoescape' => true
        );

        $this->twig = new Twig\Environment($loader);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());

    }

    /**
     * Funkce nacita vsechny twig sablony.
     * Vraci pole s adresami sablon
     * @return array
     */
    function loadAllTemplates(): array
    {

        $path = '../' . DIRECTORY_VIEWS;
        $result = array();
        $temp = scandir($path);

        foreach ($temp as $line){
            if (strpos($line, DEFAULT_TEMPLATE_FILE_END) != true && ($line != '.'  && $line != '..')) {
                $filesInFolder = scandir($path . $line . '/');
                foreach ($filesInFolder as $file){
                    if ($file != '.'  && $file != '..') {
                        array_push($result, $path . $line);
                    }
                }
            }elseif ($line != '.'  && $line != '..') {
                array_push($result, substr($path, 0, -1));
            }
        }

        return $result;
    }

    /**
     * Funkce overi, zda je uzivatel prihlaseny. Pokud ne, presmeruje ho na login stranku
     */
    private function checkUserLogin($class){
        switch ($class){
            case "LoginController": return;
            case "Error404Controller": return;
            case "RouterController": return;
            default:
                if(!(isset($_SESSION['name'])) || empty($_SESSION['surname'])){
                    $this->redirect(login);
                }
        }
    }

    /**
     * Funce overuje, jestli ma uzivatel dostatecnou roli na to, aby mohl stranku zobrazit
     * @param $class stranka, ktera se nacita
     * @param $roles role, ktere jsou povolene
     * @return bool vraci true nebo false podle toho, jestli má uživatel správná prave.. pokud ne vrati false
     * pokud promena $roles je prazdne pole, uzivatel ma pravo tutu stranku zobrazit
     */
    private function checkRole($class, $roles){

        switch ($class){
            case "LoginController": return true;
            case "Error404Controller": return true;
            default:
                if($roles[0] == "all") {
                    return true;
                }
                foreach ($roles as $oneRole){
                    if($oneRole == $_SESSION['userRole']){
                        return true;
                    }
                }
                return false;
        }
    }

    private function getAllowRoles($controller){
        $result = $this->from_camel_case($controller);
        //echo $result;
        foreach (NAV_ITEMS as $item) {
            if ($item[3] == $result){
                return $item[2];
            }
        }
        return array();
    }

    function from_camel_case($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return str_replace("-controller","",implode('-', $ret));
    }
}