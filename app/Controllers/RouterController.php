<?php

class RouterController extends MainController
{
// Instance controlleru
    protected $kontroler;

    // Metoda převede pomlčkovou variantu controlleru na název třídy
    private function pomlckyDoVelbloudiNotace($text)
    {
        $veta = str_replace('-', ' ', $text);
        $veta = ucwords($veta);
        $veta = str_replace(' ', '', $veta);
        return $veta;
    }

    // Naparsuje URL adresu podle lomítek a vrátí pole parametrů
    private function parsujURL($url)
    {
        // Naparsuje jednotlivé části URL adresy do asociativního pole
        $naparsovanaURL = parse_url($url);
        // Odstranění počátečního lomítka
        $naparsovanaURL["path"] = ltrim($naparsovanaURL["path"], "/");
        $naparsovanaURL["path"] = rtrim($naparsovanaURL["path"], "/");
        // Odstranění bílých znaků kolem adresy
        $naparsovanaURL["path"] = trim($naparsovanaURL["path"]);
        // Rozbití řetězce podle lomítek
        $rozdelenaCesta = explode("/", $naparsovanaURL["path"]);
        return $rozdelenaCesta;
    }

    // Naparsování URL adresy a vytvoření příslušného controlleru
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