<?php

/**
 * Index
 * php version 8
 *
 * @category Index
 * @package  Index
 * @author   Ronald Bello <ronaldbello@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     https://ronaldrbb.github.io/RonaldRBB/
 */

/**
 * Módulos
 * -----------------------------------------------------------------------------
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/src/functions.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/controllers/main.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/controllers/home.php");
/**
 * Denegar acceso al archivo directamente
 * -----------------------------------------------------------------------------
 */
\TCD\functions\denyDirectAccess();
/**
 * Principal
 * -----------------------------------------------------------------------------
 * Ejecución.
 */
\TCD\controllers\main();
