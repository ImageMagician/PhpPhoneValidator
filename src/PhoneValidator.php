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
        // Remove non-digits
        $digits = preg_replace('/\D/', '', $phone);

        //must be 10 digits
        if (strlen($digits) !== 10) {
            return false;
        }

        $areaCode = substr($digits, 0, 3);
        $exchange = substr($digits, 3, 3);

        // Check area code
        if (!isset($exchange, $this->areaCodes[$areaCode])) {
            return false;
        }

        // Check invalid exchanges
        if (in_array($exchange, $this->invalidExchanges)) {
            return false;
        }

        return true;
    }

    public function getStateByAreaCode(string $areaCode) : ?string {
        return $this->areaCodes[$areaCode]['state'] ?? null;
    }

    public function getUtcByAreaCode(string $areaCode) : ?string {
        return $this->areaCodes[$areaCode]['utc'] ?? null;
    }
}