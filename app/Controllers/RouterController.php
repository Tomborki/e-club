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

        //print_r($naparsovanaURL);

        if (empty($naparsovanaURL[0])){
            $this->redirect('login');
        }

        // kontroler je 1. parametr URL
        $tridaKontroleru = $this->pomlckyDoVelbloudiNotace(array_shift($naparsovanaURL)) . 'Controller';

        if (file_exists('../' . DIRECTORY_CONTROLLERS . $tridaKontroleru . ".php")){
            $this->kontroler = new $tridaKontroleru;
        } else{
           // $this->redirect('error404');
        }

        // Volání controlleru
         $this->kontroler->zpracuj($naparsovanaURL);

       // print_r($this->kontroler);

        $this->twig->render('rozlozeni.html.twig', $this->data);
    }

}