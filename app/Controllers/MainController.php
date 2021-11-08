<?php

abstract class MainController
{
    protected $data = array();
    protected $view = "";
    protected $header = array('titulek' => '', 'klicova_slova' => '', 'popis' => '');
    protected $twig;

    public function __construct()
    {
        $this->loadTwig();
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

    public function redirect($url)
    {
        header("Location: /$url");
        header("Connection: close");
        exit;
    }

    public function displayTwig(){
        $controllerName = strtolower(str_replace('Controller', '', get_called_class()));
        $this->twig->display($controllerName . '.html.twig', $this->data);
    }

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
}