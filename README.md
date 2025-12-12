# US Phone Validator
Validate US phone numbers against area codes and invalid exchange codes in a PHP project.

## Usage
```php
use PhoneValidator\PhoneValidator;

$validator = new PhoneValidator();
var_dump($validator->validate('2125551234')); // false (555 invalid)
var_dump($validator->validate('2122345678')); // true
```

## Data
- data/invalid_exchanges.json - All invalid 3-digit exchanges (NXX)

- data/usa_area_codes.json - All US area codes with UTC offsets
