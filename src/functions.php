<?php

/**
 * Scr - Functions
 * php version 8
 *
 * @category Functions
 * @package  Scr
 * @author   Ronald Bello <ronaldbello@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     https://ronaldrbb.github.io/RonaldRBB/
 */

namespace TCD\functions;

/**
 * Denegar acceso directo
 * -----------------------------------------------------------------------------
 * Denegar el acceso directo al archivo.
 *
 * @return void
 */
function denyDirectAccess()
{
    if (basename($_SERVER["PHP_SELF"]) == basename(__FILE__)) {
        denyAccess();
        exit;
    }
}
/**
 * Denegar Acceso
 * -----------------------------------------------------------------------------
 *
 * @return void
 */
function denyAccess()
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
