<?php

const DEBUG_MODE = TRUE;

//------------------------- NASTAVENI STRANKY --------------------------------

/** Hlavni barva stranky */
const MAIN_APP_COLOR = '#003ADD';

/**
 * Polozky v navigaci stranky
 * array(
 *       Nazev stranky
 *       jmeno ikonky na boxicons https://boxicons.com/ - (bx bx-{jmeno})
 *       pole s cislama povolenych roly (all = vsichni)
 *       odkaz na controller
 * )
 */
const NAV_ITEMS = array(
    array('Hlavní stránka', 'home-alt', array('all'), 'home'),
    array('Mé pokuty', 'coin', array('all'), 'my-fines'),
    array('Mé zápasy', 'football', array('all'), 'my-matches'),
    array('Oddíly', 'sitemap', array('all'), 'divisions'),
    array('Uživatelé', 'user', array(1), 'users'),
    array('Role', 'shield-quarter', array(1), 'roles'),
    array('Administrace', 'cog', array(1), 'administration'),
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

/** Tabulka s pokutami. */
const TABLE_FINER = "finer";

/** Tabulka s typama pokut. */
const TABLE_TYPE_FINES = "typeFines";

/** Tabulka se zapasama. */
const TABLE_MATCHES = "matches";

/** Tabulka s tymama. */
const TABLE_TEAMS = "teams";

/** Tabulka s oddilama. */
const TABLE_DIVISIONS = "divisions";


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
