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
#### Output

<table style="border:1px solid #3C3733;border-collapse:collapse;font:normal 12px Arial; width:98%;margin:2px;">
<tr style="border:1px solid #3C3733;background-color:#36f;color:#fff;font:normal 12px Arial;text-align:left;">
<td style="border:1px solid #3C3733;background-color:#36f;color:#fff;font:normal 12px Arial;text-align:left;" colspan="3"><b> [18.12.2018 19:54:41,569](342k/362k) Debug::write &quot;debugtest1.php Line 12 </b></td></tr>
<tr><td style="width:30px;border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;"><b> 0</b></td><td style="width:165px;border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;">string(8) ASCII</td><td style="border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;">&quot;examples&quot;</td></tr><tr><td style="width:30px;border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;"><b> 1</b></td><td style="width:165px;border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;">string(27) UTF-8</td><td style="border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;">&quot;Avenue des Champs-Élysées&quot;</td></tr><tr><td style="width:30px;border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;"><b> 2</b></td><td style="width:165px;border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;">array(3)</td><td style="border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;text-align:left;font:normal 12px Arial;">array (<br>
&nbsp;&nbsp; &#039;country&#039; =&gt; &quot;France&quot;<br>
&nbsp;&nbsp; &#039;city&#039; =&gt; &quot;Paris&quot;,<br>
&nbsp;&nbsp;  &#039;street&#039; =&gt; &quot;Avenue des Champs-Élysées&quot;,<br>
)
</td></tr></table>

## German Doc
http://jspit.de/?page=debug 

## Demo and Test
http://jspit.de/check/phpcheck.class.debug.php
