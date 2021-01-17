# Debug - Smart PHP Debug Class

Debug is a smart class for debugging in php. 
The outputs are sent to the browser or written in own files.

## Features

- Shows a lot of information about variables, scalar types and arrays as valid PHP code that can be used for testing
- Backtrace info (calling function / method / file with line numbers)
- Time (absolute and relative)
- Memory requirements
- Shows preview images for resources of the type gd
- Output to all web browsers without plugins
- Logging to a custom file
- Independent (Linux,Window, PHP from V5.6 up to V7.x)

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

## Class-Info

| Info | Value |
| :--- | :---- |
| Declaration | class Debug |
| Datei | class.debug.php |
| Date/Time modify File | 2019-11-07 07:29:59 |
| File-Size | 32 KByte |
| MD5 File | e904e765791cee5ec4ef139f95b7c131 |
| Version | 2.44 (const VERSION = 2.44) |
| Date | 2019-11-07 |

## Public Methods

| Methods and Parameter | Description/Comments |
| :-------------------- | :------------------- |
| public static function log($OnOff_or_File = true, $OnOff_err_log = null) | start/stop logging display/logfile<br>log(false) : all methods will do nothing , System-Log unchanged<br>log(&quot;filename&quot;) : start and write debug-info into logfile<br>log(&quot;+filename&quot;) : start and write debug-info into logfile append<br>log(&quot;filename&quot;,true): start and write debug-info and system Warnings and Errors into logfile<br>log(&#039;&#039;) : stop logfile, next write will show on display |
| public static function catchError() | aktivate catch Error and Exceptions |
| public static function save(/** $var1, $var2, .. **/) | save the loginformation in a buffer or logfile, no output<br>$var1, $var2, .. **/  |
| public static function write(/** $var1, $var2, .. **/) | general output for saved and current debug-infos on display or logfile<br>$var1, $var2, .. **/  |
| public static function writeIf($condition = null/** $var1, $var2, .. **/) | general output for saved and current debug-infos on display or logfile<br>if $condition == true<br>if <br>$var1, $var2, .. **/  |
| public static function wrc(/** $var1, $var2, .. **/) | write with color red<br>$var1, $var2, .. **/  |
| public static function writePre(/** $var1, $var2, .. **/) | general output for saved and current debug-infos on display or logfile<br>display text in &lt;pre&gt;-tags<br>$var1, $var2, .. **/  |
| public static function writeHex(/** $var1, $var2, .. **/) | display strings as hex<br>$var1, $var2, .. **/  |
| public static function html(/** $var1, $var2, .. **/) | get the loginformation from buffer and delete buffer<br>return html-table (up to V1.95 getClean() )<br>$var1, $var2, .. **/  |
| public static function stop($condition = null /**, $var1, $var2, .. **/) | general output like write a output for saved and actual debug-infos on display or logfile <br>$condition : if condition ist true or null then write and throw a exception, in the other case do nothing<br>if $condition is int, write and stop at the $condition call<br>if $condition is int and 0 or &lt;0 do nothing (also no decrement stopCounter)<br>, $var1, $var2, .. **/  |
| public static function resetStopCounter() | Reset for Stop Counter |
| public static function clear() | clear internal buffer, deletes all information that have been saved<br>logfile-infos not delete |
| public static function systeminfo() | Info |
| public static function crc($value) | Returns the 32-bit CRC checksum of the argument as a hex string of length 8<br>accept int, float, string, arrays and objects, no resource |
| public static function microSleep($microSeconds) | sleep $microSeconds, return real count microseconds |
| public static function TypeInfo($obj, $options = array()) | TypeInfo returns a string type of (len,class,resource-typ..) |
| public static function strhex($s) | return a string as hexadecimal like &#039;\x61\x62..&#039; |
| public static function UniDecode($strUplus) | return char for Unicode-Format U+20ac (U+0000..U+3FFF) |
| public static function strToUnicode($string, $showAsciiAsUnicode = false) | return PHP unicode string Format &#039;\u{20ac}\u{41}&#039; for all multibyte chars of string<br>for non utf8 chars returns &quot;\xhh&quot; |
| public static function detect_encoding($string) | tries to determine the charset of string<br>return p.E. &#039;UTF-8&#039;,&#039;UTF-8 BOM&#039;,&#039;ISO-8859-1&#039;,&#039;ASCII&#039; <br>faster as mb_detect_encoding($string,&#039;ASCII, UTF-8, ISO-8859-1&#039;,true); |
| public static function setTitleColor($backgroundColor) | set a new color for background of title |
| public static function setImgStyle($style) | set style for gd-resource |
| public static function setGdOutputFormat($Ident) | set $gdOutputFormat<br>param Ident : &quot;png&quot; or &quot;jpg&quot; |
| public static function switchLog($filePath = NULL) | Activate the log depending on the contents of the file<br>If content &quot;1&quot; Log is enabled, else if content &quot;0&quot; disabled<br>If param is bool, permanent activ/deaktive with true/false<br>Default filePath: __DIR__.&#039;/debug.switch&#039; |
| public static function deleteLogFile() | Delete a Logfile set with debug::log(&quot;filename&quot;)<br>debug::write after deleteLogFile will display on screen |
| public static function deleteLastLogFileSegment() | Delete last Segment from Logfile<br>@return bool true if logfile cut, false if not  |
| public static function getLogFileName() | get the current LogFileName |
| public static function isOn() | return true if debug mode is on |
| public static function setTimestamp($index = 0) | Start stopwatch |
| public static function getTimestampDiff($index = 0) | get Time in µs from start stowatch to now<br>return int time µs or false if index not valid |
| public static function getErrorTypName($type) | Get a Name for Error-Type<br>@param int $type<br>@return string |
| public static function shutDownHandle($flag = false) |  |


## Public Propertys

| Property and Defaults | Description/Comments |
| :-------------------- | :------------------- |
|  public static $showSpecialChars = true;  | convert special chars in hex-code |
|  public static $real_time_output = false; | shows the debug info promptly |
|  public static $exitOnStop = true; | if $exitOnStop is true, stop method make a exit after output<br>if $exitOnStop is false, stop method return true after output |
|  public static $stringCut = 180; | cut Strings with more than $stringCut chars and $showSpecialChars = true |

## Constants

| Declaration/Name | Value | Description/Comments |
| :--------------- | :---- | :------------------- |
|  const VERSION = &quot;2.4&quot;; | &#039;2.4&#039; |   |
