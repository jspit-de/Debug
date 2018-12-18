# Debug - Smart PHP Debug Class

Debug is a smart class for debugging in php. 
The outputs are sent to the browser or written in own files.

## Features


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
