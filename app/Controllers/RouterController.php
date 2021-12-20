<?php

class RouterController extends MainController
{

    protected $kontroler;

    /**
     * @param $text
     * @return array|string|string[]
     * Metoda prevadi string z pomlcek do velbloudi notace
     */
    private function pomlckyDoVelbloudiNotace($text)
    {
        $veta = str_replace('-', ' ', $text);
        $veta = ucwords($veta);
        $veta = str_replace(' ', '', $veta);
        return $veta;
    }

    /**
     * @param $url
     * @return false|string[]
     * Metoda naparsuje url
     */
    private function parsujURL($url)
    {
        // Naparsuje jednotlivé části URL adresy do asociativního pole
        $naparsovanaURL = parse_url($url);
        $naparsovanaURL["path"] = ltrim($naparsovanaURL["path"], "/");
        $naparsovanaURL["path"] = rtrim($naparsovanaURL["path"], "/");
        $naparsovanaURL["path"] = trim($naparsovanaURL["path"]);
        $rozdelenaCesta = explode("/", $naparsovanaURL["path"]);
        return $rozdelenaCesta;
    }

    /**
     * @param $parametry
     * Funkce zpracovava vstupni data a predava sablone
     */
    public function zpracuj($parametry)
    {
        $naparsovanaURL = $this->parsujURL($parametry[0]);



        if (empty($naparsovanaURL[0])){
            $this->redirect('login');
        }

        // kontroler je 1. parametr URL
        $tridaKontroleru = $this->pomlckyDoVelbloudiNotace($naparsovanaURL[0]) . 'Controller';



        if (file_exists('../' . DIRECTORY_CONTROLLERS . $tridaKontroleru . ".php")){
            $this->kontroler = new $tridaKontroleru;
        } else{
            if(!DEBUG_MODE){
                $this->redirect('error404');
            }
        }

        if(isset($naparsovanaURL[1])){
            $this->kontroler->diferendTemplate = $naparsovanaURL[1];
        }

        // Volání controlleru

        $this->kontroler->zpracuj($naparsovanaURL);


        //var_dump($this->kontroler);

        $this->twig->render('rozlozeni.html.twig', $this->data);
    }

}