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
function collectionsDataGet()
{
    $resourcesDir = $_SERVER["DOCUMENT_ROOT"] . "/resources";
    $files = array_slice(scandir($resourcesDir), 2);
    $collections = [];
    foreach ($files as $file) {
        if (strpos($file, "REV") === false) {
            $collection = new \TCD\classes\Collection($resourcesDir, $file);
            $collection->parseData();
            $collections[] = [
                "fileName" => $file,
                "totalAmount" => $collection->totalAmount,
                "totalRecords" => $collection->totalRecords,
                "averageAmount" => $collection->getAverage(),
                "totalByPaymentMethod" => $collection->getTotalByPaymentMethod(),
                "data" => $collection->data,
            ];
        }
    }
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json');
    echo json_encode($collections);
}
