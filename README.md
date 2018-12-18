# Debug - Smart PHP Debug Class

Debug is a smart class for debugging in php. 
The outputs are sent to the browser or written in own files.

## Features

- Shows a lot of information about variables, scalar types and arrays as valid PHP code that can be used for testing
- Backtrace info (calling function / method / file with line numbers)
- Time (absolute and different)
- Memory requirements
- Global switch for debug on/off
- Global selection for output to browser or to custom file
- Independent (Linux,Window, PHP from V5.3.8 up to V7.2)

## How to use it

Easy, just download and include the class file and and make outputs wherever you want.

### Simple example 1

```php
require PATH_TO_CLASS.'class.debug.php';

$street = "Avenue des Champs-Élysées";
$array = [
  "country" => "France",
  "city" => "Paris",
  "street" => $street,
];
  
debug::write('examples', $street, $array);
```


## German Doc
http://jspit.de/?page=debug 

## Demo and Test
http://jspit.de/check/phpcheck.class.debug.php
