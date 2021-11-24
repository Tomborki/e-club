<?php

abstract class MainController
{
    protected $data = array();
    protected $view = "";
    protected $header = array('titulek' => '', 'klicova_slova' => '', 'popis' => '');
    protected $twig;
    protected $db;

    public function __construct()
    {
        $this->loadTwig();
        $this->db = new DbModel();
    }

    abstract function zpracuj($parametry);

    public function showView()
    {
        if ($this->view)
        {
            extract($this->data);
            require('../' . DIRECTORY_VIEWS . $this->view . ".html.twig");
        }
    }

    /**
     * Funkce presmerovava stranku na stranku zadanou v parametru
     * @param $url
     */
    public function redirect($url)
    {
        header("Location: /$url");
        header("Connection: close");
        exit;
    }

    /**
     * Funkce vypisuje data do prislusne sablony. Zaroven overuje, zda je uzivatel prihlaseny
     */
    public function displayTwig(){

        // ----------------- Check login ------------------
        if(get_called_class() != 'LoginController'){
            $this->checkUserLogin();
        }

        $controllerName = strtolower(str_replace('Controller', '', get_called_class()));

        //Predani zakladnich informaci o strance
        if(isset($_SESSION['name']) && isset($_SESSION['surname'])){
            $this->data['name'] = $_SESSION['name'];
            $this->data['surname'] = $_SESSION['surname'];
        }

        $this->data['pageName'] = $controllerName;
        $this->data['mainColor'] = MAIN_APP_COLOR;

        $this->twig->display($controllerName . '.html.twig', $this->data);
    }

    /**
     * Funkce nacita samostatny twig
     */
    function loadTwig(){

        $loader = new Twig\Loader\FilesystemLoader($this->loadAllTemplates());
        // set up environment
        $params = array(
            'cache' => "../cache",
            'auto_reload' => true, // disable cache
            'autoescape' => true
        );
        $this->twig = new Twig\Environment($loader, $params);

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
    private function checkUserLogin(){

        if(!(isset($_SESSION['name'])) || empty($_SESSION['surname'])){
           $this->redirect(login);
        }
    }
}