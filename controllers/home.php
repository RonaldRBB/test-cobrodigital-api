<?php

/**
 * Controllers - Home
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
 * Home - Método Get
 * -----------------------------------------------------------------------------
 * Pagina Home.
 *
 * @return void
 */
function homeGet()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        \TCD\functions\denyAccess();
    }
    echo "Hello World";
}
