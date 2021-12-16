<?php

/**
 * Src - Classes - Collection
 * php version 8
 *
 * @category Classes
 * @package  Src
 * @author   Ronald Bello <ronaldbello@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     https://ronaldrbb.github.io/RonaldRBB/
 */

namespace TCD\classes;

/**
 * Cobranza
 * -----------------------------------------------------------------------------
 * Clase para manejar las cobranzas.
 *
 * @category Classes
 * @package  Src
 * @author   Ronald Bello <ronaldbello@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     https://ronaldrbb.github.io/RonaldRBB/
 */
class Collection
{
    private $fileDir = null;
    private $rawData = null;
    private $data = array();
    public $header = null;
    public $footer = null;
    /**
     * Constructor
     * -------------------------------------------------------------------------
     *
     * @return void
     */
    public function __construct($fileDir)
    {
        $this->fileDir  = $fileDir;
        $this->loadFile();
        $this->parseData();
    }
    /**
     * Cargar Archivo
     * -------------------------------------------------------------------------
     * Cargar el archivo de cobranza.
     *
     * @return void
     */
    private function loadFile()
    {
        if (file_exists($this->fileDir)) {
            $this->rawData = file_get_contents($this->fileDir);
        } else {
            throw new \Exception("Archivo no encontrado: $this->fileDir");
        }
        return [];
    }
    /**
     * Analizar Datos
     * -------------------------------------------------------------------------
     */
    private function parseData()
    {
        $data = explode("\n", $this->rawData);
        $this->header = $data[0];
        $this->footer = $data[count($data) - 2];
        // array_shift($data);
        // array_pop($data);
        // array_pop($data);
        $data = array_slice($data, 1, count($data) - 3);
        for ($i = 1; $i < count($data) - 1; $i++) {
            $this->data[] = $this->parseRecord($data[$i]);
        }
    }
    /**
     * Analizar Registro
     * -------------------------------------------------------------------------
     */
    private function parseRecord($record)
    {
        $parsedRecord = array();
        $parsedRecord["clientId"] = trim(substr($record, 4, 22));
        $parsedRecord["univocalReference"] = trim(substr($record, 53, 15));
        $parsedRecord["collectionDay"] = substr($record, 294, 8);
        $parsedRecord["totalAmount"] = $this->convertToDecimal(substr($record, 302, 14));
        return $parsedRecord;
    }
    /**
     * Convert to Decimals
     * -------------------------------------------------------------------------
     * Convert From String to Decimal.
     */
    private function convertToDecimal($string)
    {
        $decimal = substr($string, 0, 12) . "." . substr($string, 12, 2);
        $decimal = (float) number_format($decimal, 2, '.', '');
        return $decimal;
    }
}
