<?php

namespace PhoneValidator;

class PhoneValidator
{
    private function loadAreaCodes() : array {
        $json = file_get_contents(__DIR__ . "/../data/usa_area_codes.json");
        return json_decode($json, true);
    }

    private function loadInvalidExchanges() : array {
        $json = file_get_contents(__DIR__ . "/../data/invalid_exchanges.json");
        $decoded = json_decode($json, true);
        return $decoded['invalid_exchanges'];
    }

    public function validate(string $phone) : bool {

        $digits = $this->removeNonNumeric($phone);

        //must be 10 digits
        if (strlen($digits) !== 10) {
            return false;
        }

        // Check area code
        if (!$this->validAreaCode($digits)) {
            return false;
        }

        // Check invalid exchanges
        if ($this->validExchange($digits)) {
            return false;
        }

        return true;
    }

    public function validAreaCode(string $phone) : bool {
        $areaCodes = $this->loadAreaCodes();
        $areaCode = $this->areaCodeSubString($phone);
        return array_key_exists($areaCode, $areaCodes);
    }

    public function validExchange(string $phone) : bool {
        $loadExchanges = $this->loadInvalidExchanges();
        $exchange = $this->exchangeSubString($phone);
        return in_array($exchange, $loadExchanges);
    }

    private function removeNonNumeric(string $phone) : bool {
        // Remove non-digits
        return preg_replace('/\D/', '', $phone);
    }

    public function formatPhone(string $phone) : string {
        $digits = $this->removeNonNumeric($phone);
        $areaCode = $this->areaCodeSubString($digits);
        $exchange = $this->exchangeSubString($digits);
        $last = substr($digits, -4);
        return '(' . $areaCode . ')' . $exchange . '-' . $last;
    }

    private function areaCodeSubString( string $phone) : string {
        $phone = $this->removeNonNumeric($phone);
        return substr($phone, 0, 3);
    }

    private function exchangeSubString( string $phone) : string {
        $phone = $this->removeNonNumeric($phone);
        return substr($phone, 3, 3);
    }

    public function getStateByAreaCode(string $areaCode) : ?string {
        return $this->areaCodes[$areaCode]['state'] ?? null;
    }

    public function getUtcByAreaCode(string $areaCode) : ?string {
        return $this->areaCodes[$areaCode]['utc'] ?? null;
    }
}