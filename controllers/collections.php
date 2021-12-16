<?php

/**
 * Controllers - Collections
 * php version 8
 *
 * @category Controller
 * @package  Controllers
 * @author   Ronald Bello <ronaldbello@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     https://ronaldrbb.github.io/RonaldRBB/
 */

namespace TCD\controllers;

/**
 * Cobranzas - Método Get
 * -----------------------------------------------------------------------------
 * Pagina de Cobranzas.
 *
 * @return void
 */
function collectionsGet()
{
    global $config;
    $_SERVER["REQUEST_METHOD"] !== "GET" ? \TCD\functions\denyAccess() : null;
    $fileDir0 = $_SERVER["DOCUMENT_ROOT"] . "/resources/" . $config["collectionFile"];
    $fileDir1 = $_SERVER["DOCUMENT_ROOT"] . "/resources/" . $config["reversalFile"];
    $fileDir2 = $_SERVER["DOCUMENT_ROOT"] . "/resources/" . $config["888File"];
    $collection = new \TCD\classes\Collection($fileDir0);
}
