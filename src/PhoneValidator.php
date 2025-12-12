<?php

namespace PhoneValidator;

class PhoneValidator
{
    private array $areaCodes = [];
    private array $invalidExchanges = [];

    public function __construct() {
        $this->loadAreaCodes();
        $this->loadInvalidExchanges();
    }

    private function loadAreaCodes() : void {
        $json = file_get_contents(__DIR__ . "/../data/usa_area_codes.json");
        $this->areaCodes = json_decode($json, true);
    }

    private function loadInvalidExchanges() : void {
        $json = file_get_contents(__DIR__ . "/../data/invalid_exchanges.json");
        $this->invalidExchanges = json_decode($json, true);
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
        if ($this->invalidExchange($digits)) {
            return false;
        }

        return true;
    }

    public function validAreaCode(string $phone) : bool {
        $areaCode = substr($phone, 0, 3);
        return isset($this->areaCodes[$areaCode]);
    }

    public function invalidExchange(string $phone) : bool {
        $exchange = substr($phone, 3, 3);
        return !in_array($exchange, $this->invalidExchanges);
    }

    private function removeNonNumeric(string $phone) : bool {
        // Remove non-digits
        return preg_replace('/\D/', '', $phone);
    }

    public function getStateByAreaCode(string $areaCode) : ?string {
        return $this->areaCodes[$areaCode]['state'] ?? null;
    }

    public function getUtcByAreaCode(string $areaCode) : ?string {
        return $this->areaCodes[$areaCode]['utc'] ?? null;
    }
}