<?php

// Pripojeni k databazi ////

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
const TABLE_INTRODUCTION = "orionlogin_mvc_introduction";

/** Tabulka s uzivateli. */
const TABLE_USER = "orionlogin_mvc_user";


//// Dostupne stranky webu ////

/** Adresar kontroleru. */
const DIRECTORY_CONTROLLERS = "app\Controllers";

/** Adresar modelu. */
const DIRECTORY_MODELS = "app\Models";

/** Adresar sablon */
const DIRECTORY_VIEWS = "app\Views";


/** Klic defaultni webove stranky. */
const DEFAULT_WEB_PAGE_KEY = "uvod";


?>