<?php

class Collinsharper_Canpost_Model_Currency_Import_Webservicex extends Mage_Directory_Model_Currency_Import_Webservicex
{
    public function chGetRate($currencyFrom, $currencyTo)
    {
        return $this->_convert($currencyFrom, $currencyTo);
    }
}