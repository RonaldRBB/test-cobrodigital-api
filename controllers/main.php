<?php

/**
 * Controllers - Main
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
 * Controlador Principal
 * -----------------------------------------------------------------------------
 * Controlador de Paginas
 *
 * @return void
 */
function main()
{
    if ($_SERVER["REQUEST_URI"] == "/home") {
        \TCD\controllers\homeGet();
    } else {
        \TCD\functions\denyAccess();
    }
}
