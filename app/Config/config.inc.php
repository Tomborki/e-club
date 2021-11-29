<?php

const DEBUG_MODE = true;

//------------------------- NASTAVENI STRANKY --------------------------------

/** Hlavni barva stranky */
const MAIN_APP_COLOR = '#003ADD';

/**
 * Polozky v navigaci stranky
 * array(
 *       Nazev stranky
 *       jmeno ikonky na boxicons https://boxicons.com/ - (bx bx-{jmeno})
 *       pole s cislama povolenych roly
 *       odkaz na controller
 * )
 */
const NAV_ITEMS = array(
    array('Hlavní stránka', 'home-alt', array(1,2,3), 'home'),
    array('Mé pokuty', 'coin', array(1,2,3), 'my-fines'),
    array('Mé zápasy', 'football', array(1,2,3), 'my-matches'),
    array('Uživatelé', 'user', array(3), 'users'),
    array('Role', 'shield-quarter', array(3), 'roles'),
    array('Nastavení', 'cog', array(3), 'settings'),
);

//------------------------------ DATABAZE ------------------------------------

/** Adresa serveru. */
const DB_SERVER = "localhost";

/** Nazev databaze. */
const DB_NAME = "e-club";

/** Uzivatel databaze. */
const DB_USER = "root";

/** Heslo uzivatele databaze */
const DB_PASS = "abcd123*";

//// Nazvy tabulek v DB ////
/** Tabulka s pohadkami. */
const TABLE_ROLES = "roles";

/** Tabulka s uzivateli. */
const TABLE_USER = "users";

//------------------------------ ADRESARE ------------------------------------

/** Adresar kontroleru. */
const DIRECTORY_CONTROLLERS = "app/Controllers/";

/** Adresar modelu. */
const DIRECTORY_MODELS = "app/Models/";

/** Adresar sablon */
const DIRECTORY_VIEWS = "app/Views/";

//------------------------------ SABLONY ------------------------------------

/** Zakonceni souboru sablon. */
const DEFAULT_TEMPLATE_FILE_END = ".twig";

?>
