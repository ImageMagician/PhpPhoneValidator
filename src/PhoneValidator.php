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
        if (!$this->validAreaCode($areaCode)) {
            return false;
        }

        // Check invalid exchanges
        if ($this->invalidExchange($exchange)) {
            return false;
        }

        return true;
    }

    public function validAreaCode(string $areaCode) : bool {
        return isset($this->areaCodes[$areaCode]);
    }

    public function invalidExchange(string $exchange) : bool {
        return !in_array($exchange, $this->invalidExchanges);
    }

    public function getStateByAreaCode(string $areaCode) : ?string {
        return $this->areaCodes[$areaCode]['state'] ?? null;
    }

    public function getUtcByAreaCode(string $areaCode) : ?string {
        return $this->areaCodes[$areaCode]['utc'] ?? null;
    }
}