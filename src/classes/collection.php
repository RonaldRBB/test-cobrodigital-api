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
    public $fileName = null;
    public $data = array();
    public $header = null;
    public $footer = null;
    public $totalAmount = 0;
    public $totalRecords = 0;
    /**
     * Constructor
     * -------------------------------------------------------------------------
     *
     * @return void
     */
    public function __construct($resourcesDir = null, $fileName = null)
    {
        if ($resourcesDir == null) {
            $resourcesDir = $_SERVER["DOCUMENT_ROOT"] . "/resources";
        }
        $this->fileDir = $resourcesDir . "/" . $fileName;
        $this->fileName = $fileName;
        $this->loadFile();
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
            $rawData = explode("\n", file_get_contents($this->fileDir));
            $this->rawData = $this->cleanData($rawData);
        } else {
            throw new \Exception("Archivo no encontrado: $this->fileDir");
        }
        return [];
    }
    /**
     * Analizar Datos
     * -------------------------------------------------------------------------
     */
    public function parseData()
    {
        $data = $this->rawData;
        $this->header = $this->normalizeString($data[0]);
        $this->footer = $this->normalizeString($data[count($data) - 1]);
        $data = array_slice($data, 1, count($data) - 1);
        foreach ($data as $record) {
            if (
                substr($record, 0, 4) == "DATO" ||
                substr($record, 0, 4) == "0370"
            ) {
                $this->data[] = $this->parseRecord($record);
            }
        }
    }
    /**
     * Analizar Registro
     * -------------------------------------------------------------------------
     */
    private function parseRecord($record)
    {
        $parsedRecord = [];
        $parsedRecord["transNumber"] = $this->getRecordTransactionNumber($record);
        $parsedRecord["amount"] = $this->getRecordAmount($record);
        $parsedRecord["id"] = $this->getRecordId($record);
        $parsedRecord["paymentDate"] = $this->getRecordPaymentDate($record);
        $parsedRecord["paymentMethod"] = $this->getRecordPaymentMethod($record);
        $this->totalAmount += $parsedRecord["amount"];
        $this->totalRecords++;
        return $parsedRecord;
    }

    /**
     * Obtener Numero de Transaccion de Registro
     * -------------------------------------------------------------------------
     *
     * @param string $record
     * @return string
     */
    private function getRecordTransactionNumber($record)
    {
        switch (substr($record, 0, 4)) {
            case "DATO":
                return substr($record, 41, 8);
                break;
            case "0370":
                return substr($record, 52, 15);
                break;
        }
    }
    /**
     * Obtener Monto del Registro
     * -------------------------------------------------------------------------
     *
     * @param string $record
     * @return float
     */
    private function getRecordAmount($record)
    {
        switch (substr($record, 0, 4)) {
            case "DATO":
                return $this->convertToDecimal(substr($record, 77, 10));
                break;
            case "0370":
                return $this->convertToDecimal(substr($record, 302, 14));
                break;
        }
    }
    /**
     * Obtener Id del Registro
     * -------------------------------------------------------------------------
     *
     * @param string $record
     * @return string
     */
    private function getRecordId($record)
    {
        switch (substr($record, 0, 4)) {
            case "DATO":
                return trim(substr($record, 58, 14));
                break;
            case "0370":
                return substr($record, 52, 15);
                break;
        }
    }
    /**
     * Obtener Fecha de Pago del Registro
     * -------------------------------------------------------------------------
     *
     * @param string $record
     * @return string
     */
    private function getRecordPaymentDate($record)
    {
        switch (substr($record, 0, 4)) {
            case "DATO":
                return $this->normalizeDate(substr($record, 224, 6));
                break;
            case "0370":
                return $this->normalizeDate(substr($record, 294, 8));
                break;
        }
    }
    /**
     * Método de Pago del Registro
     * -------------------------------------------------------------------------
     */
    private function getRecordPaymentMethod($record)
    {
        switch (substr($record, 0, 4)) {
            case "DATO":
                return $this->normalizePaymentMethod(substr($record, 247, 2));
                break;
            case "0370":
                return $this->normalizePaymentMethod(null);
                break;
        }
    }
    /**
     * Normalizar Método de Pago del Registro
     * -------------------------------------------------------------------------
     */
    private function normalizePaymentMethod($method)
    {
        switch ($method) {
            case "00":
                return ["id" => "00", "name" => "Efectivo"];
                break;
            case "90":
                return ["id" => "90", "name" => "Tarjeta de Débito"];
                break;
            case "99":
                return ["id" => "99", "name" => "Tarjeta de Crédito"];
            default:
                return ["id" => "desconocido", "name" => "Desconocido"];
                break;
        }
    }
    /**
     * Obtener Total Cobranza por Método de Pago
     * -------------------------------------------------------------------------
     *
     * @param string $method
     * @return array
     */
    public function getTotalByPaymentMethod()
    {
        $paymentMethods = [];
        foreach ($this->data as $record) {
            $paymentMethod = $record["paymentMethod"];
            for ($i = 0; $i < count($paymentMethods); $i++) {
                if ($paymentMethods[$i]["id"] == $paymentMethod["id"]) {
                    $paymentMethods[$i]["name"] = $paymentMethod["name"];
                    $paymentMethods[$i]["recordsQuantity"]++;
                    $paymentMethods[$i]["totalAmount"] += $record["amount"];
                    $paymentMethods[$i]["averageAmount"] =
                        $paymentMethods[$i]["totalAmount"] /
                        $paymentMethods[$i]["recordsQuantity"];
                    break;
                }
            }
            if ($i == count($paymentMethods)) {
                $paymentMethods[] = [
                    "id" => $paymentMethod["id"],
                    "name" => $paymentMethod["name"],
                    "recordsQuantity" => 1,
                    "totalAmount" => $record["amount"],
                    "averageAmount" => $record["amount"]
                ];
            }
        }
        return $paymentMethods;
    }
    /**
     * Obtener Promedio por Registro
     * -------------------------------------------------------------------------
     */
    public function getAverage()
    {
        return $this->totalAmount / $this->totalRecords;
    }
    /**
     * Limpiar Información
     * -------------------------------------------------------------------------
     * Elimina los saltos de linea y los espacios en blanco de la ultima linea.
     *
     * @param array $data
     * @return array
     */
    private function cleanData($data)
    {
        while (count($data) > 0 && strlen($data[count($data) - 1]) <= 1) {
            array_pop($data);
        }
        return $data;
    }
    /**
     * Normalizar Cadena de Texto
     * -------------------------------------------------------------------------
     * Elimina los saltos de linea y los espacios en blanco.
     *
     * @param string $string
     * @return string
     */
    private function normalizeString($string)
    {
        $string = trim($string);
        $string = str_replace(["\r", "\n"], "", $string);
        return $string;
    }
    /**
     * Convertir a Decimales
     * -------------------------------------------------------------------------
     * Convierte de string a flotante.
     *
     * @param string $value
     * @return float
     */
    private function convertToDecimal($string)
    {
        $decimal = substr($string, 0, 12) . "." . substr($string, 12, 2);
        $decimal = (float) number_format($decimal, 2, ".", "");
        return $decimal;
    }
    /**
     * Normalizar Fecha
     * -------------------------------------------------------------------------
     * Convierte de string a objeto DateTime.
     *
     * @param string $date
     * @return string
     */
    private function normalizeDate($date)
    {
        $date = getDate(strtotime($date));
        $date = $date["year"] . "-" . $date["mon"] . "-" . $date["mday"];
        return $date;
    }
}
