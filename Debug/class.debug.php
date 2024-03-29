<?php
/**
	Simple PHP Debug Class
.---------------------------------------------------------------------------.
|  Software: Debug - Simple PHP Debug Class                                 |
|  @Version: 2.52                                                           |
|      Site: http://jspit.de/?page=debug                                    |
| ------------------------------------------------------------------------- |
| Copyright © 2010-2022, Peter Junk (alias jspit). All Rights Reserved.     |
| ------------------------------------------------------------------------- |
|   License: Distributed under the Lesser General Public License (LGPL)     |
|            http://www.gnu.org/copyleft/lesser.html                        |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
  Date Last modify : 2022-10-26
  2013-02-25: add function strhex 
  2013-05-29: new stop-Method
  2013-06-19: +DOM
  2013-07-03: + detect_encoding
  2013-08-08: + debug::$real_time_output = true 
  2013-09-17: isset closure bug -> property_exists
  2013-09-23: more file infos
  2013-09-24: + exitOnStop Attribut (V1.5)
  2013-12-17: detect_encoding +
  2014-01-22: + crc checksum
  2014-02-06: + fct,classfkt 
  2014-02-20: + Windows-1252 detect encoding
  2014-03-25: + setTitleColor, wrc (write with bg-color = red)
  2014-07-10: stdClass::__set_state -> (object)
  2014-10-31: + getClean()
  2015-02-05: + debug::$stringCut  (180 default)
  2015-12-11: modify str2print + detect_encoding
  2016-02-05: V1.96 getClean() -> html umbenannt V 1.96
  2016-04-14: V1.98 + checkPerformance, rename fkt -> wrFctCheck
  2016-08-30: V2.00 + format output xml
  2016-11-10: V2.01 + debug::switchLog
  2016-11-22: V2.02 + debug::deleteLogFile
  2016-11-23: V2.02 + debug::log("TMP"), debug::getLogFileName();
  2016-12-03: V2.03 + display image from resource(gd)
  2017-02-08: V2.04 + debug::isOn()
  2017-10-09: V2.05 + meta refresh für html-log
  2017-10-11: V2.06 fix Bug String presentation
  2017-10-20: V2.07 + opt.par (intern) modifyVarExport 
  2018-04-03: V2.1 remove small bug PHP 7.2: throw warning TypeInfo 
  2018-04-04: V2.2 + writeHex
  2018-06-08: V2.2.1 catch Error *RECURSION*
  2018-06-28: V2.2.2 + deleteLastLogFileSegment()
  2018-08-20: V2.2.3 List Elements from DomNodeList
  2018-10-01: V2.2.5 fix Bug formatOutput Simplexml
  2018-10-09: V2.2.6
  2018-10-22: V2.2.7 correct String len writeHex
  2018-11-12: V2.3 add writeIf
  2019-03-20: V2.4 add catch Error
  2019-11-04: v2.44 modify UniEncode -> strToUnicode
  2019-11-21: v2.45 add unicodeToString
  2019-12-10: v2.46 add writeUni
  2019-12-12: V2.47 add headerInfo(), isHeaderSent()
  2020-02-09: V2.48 add object-Id to typeinfo (PHP >= 7.2)
  2020-07-15: V2.49 modify writeHex, PHP > 5.5
  2021-01-27: V2.50 modify modifyVarExport
  2021-08-12: V2.51 modify recArg error handling gd
  2022-10-22: V2.52 modify Unidecode, GDImage (PHP8)
*/
if (version_compare(PHP_VERSION, '7.0', '<') ) {
  throw new Exception(htmlspecialchars(
    "Simple PHP Debug Class requires at least PHP version 7.0!",
    ENT_QUOTES,"UTF-8")
  );
}
if(ini_get('date.timezone') == "") ini_set('date.timezone','UTC');

class Debug
{
  /*
  From the Simple Debug Class you get timestamp, backtrace-info and var-info's in table view
  on display or in a logfile. The methods write, save and stop accepts a variable number of parameters.
  It is very easy to use. Some examples:
  
  * Example 1 : Show Debug-Info from scalar/array/object.. on screen  
    Debug::write($var1,$var2); 
    
  * Example 2 : Write Debug-Info into logfile 
    Debug::log("logfilename");  //Script errors are also logged in the file if the second parameter is true
    Debug::write($var1,$var2); 
    Debug::log("");  //Close logfile, next write will show on screen

  * Example 3 : Disable all functions (Debug/Log-off)
    Debug::log(false);
    Debug::log(true);   //Debug/Log on

  * Example 4 : Save Write Debug-Info into internal Buffer (User-Output is not touched) 
    echo "useroutput";
    Debug::save($var1,$var2); //no output
    echo "useroutput";
    Debug::write($var3,$var4); //show saved and current info or save in logfile

  * Example 4 : Clear the internal buffer, if it is not longer needed
    Debug:clear();

  * Example 4 : Stop (Write and exit);
    Debug:stop($condition,$var1,$var2);

  */
  
  const VERSION = "2.52";
  //convert special chars in hex-code
  public static $showSpecialChars = true;           
  //shows the debug info promptly
  public static $real_time_output = false;
  //if  $exitOnStop is true, stop method make a exit after output
  //if  $exitOnStop is false, stop method return true after output
  public static $exitOnStop = true;
  //cut Strings with more than $stringCut chars and $showSpecialChars = true
  public static $stringCut = 180;

  protected static $tableStyle = '
    border:1px solid #3C3733;border-collapse:collapse;
    font:normal 12px Arial; width:98%;margin:2px;
  '; 
  protected static $trHeadStyle = '
    border:1px solid #3C3733;background-color:#36f;
    color:#fff;font:normal 12px Arial;text-align:left;
  ';  
  protected static $tdStyle = '
    border:1px solid #3C3733;vertical-align:top;background:#fff;color:#000;
    text-align:left;font:normal 12px Arial;
  ';
  protected static $col1width = '30px';
  protected static $col2width = '165px';
  //
  protected static $debug_on_off = true;
  protected static $logfilename = "";
  protected static $recbuf = "";
  protected static $lastTime = 0;
  //system error log in the same file
  protected static $sys_err_log = false;
  protected static $log_errors = "";
  protected static $error_log = "";
  protected static $log_file_append = false;
  //
  protected static $stopCounter = null;
  //
  protected static $switchOn = true;
  //
  //protected static $gdStyle = 'max-height:20rem;max-width:20rem;background-color:#ee8;';
  protected static $gdStyle = '
    max-height:20rem;max-width:20rem;
    background-image: repeating-linear-gradient(-45deg, white , #ccc 6px);
  ';
  protected static $gdOutputFormat = 'png';
  //
  protected static $logMark = "\r\n<span id=newdebuglog>.</span><br>\r\n";
  //
  protected static $timeStamps = array();
  
  
  /*
  * start/stop logging display/logfile
  * log(false)          : all methods will do nothing , System-Log unchanged
  * log("filename")     : start and write debug-info into logfile
  * log("+filename")    : start and write debug-info into logfile append
  * log("filename",true): start and write debug-info and system Warnings and Errors into logfile
  * log('')             : stop logfile, next write will show on display
  */
  public static function log($OnOff_or_File = true, $OnOff_err_log = null) 
  {
    if(is_bool($OnOff_or_File)) {
      //Argument is true/false
      self::$debug_on_off = $OnOff_or_File; 
      if(!$OnOff_or_File) @set_error_handler(null);  //PHP < 5.5 no accept null
    }
    elseif(is_string($OnOff_or_File)) 
    { //LogFile
      if($OnOff_or_File != "") 
      { // logging part
        $filename = $OnOff_or_File;
        if( self::$log_file_append = (substr($filename,0,1)=== '+')) {
          //append mode
          $filename = substr($filename,1);
        }
        if( !self::$log_file_append || !file_exists($filename)) {
          //create a new logfile
          if($filename === "TMP") $filename = self::tmpFileName();
          $content = '<!DOCTYPE html>'."\r\n".
            '<html><head>'."\r\n".
            '<meta http-equiv="content-type" content="text/html;charset=utf-8">'."\r\n";
          if(self::$real_time_output) {
            $content .= '<meta http-equiv="refresh" content="'.(int)self::$real_time_output.'">'."\r\n";
          }
          $content .= '<title>Debug Log '.$OnOff_or_File.'</title>'."\r\n".
            '</head><body>'."\r\n";

          file_put_contents($filename,$content);  
          if((fileperms($filename) & 0666) != 0666) chmod($filename,0666); //+rw all
        }
        if(self::$log_file_append) {
          file_put_contents($filename,self::$logMark,FILE_APPEND);  
        }
        self::$logfilename = $filename;
      }
      else 
      { //empty string causes stop logging 
        self::closeLog();
      }
    }
    //check sys err log set off
    if(self::$sys_err_log && ($OnOff_err_log === false || $OnOff_or_File === '')) {
      //set back
      ini_set('log_errors', self::$log_errors);
      ini_set('error_log', self::$error_log);
    }
    //OnOff_err_log
    if(is_bool($OnOff_err_log)) {
      if($OnOff_err_log && self::$logfilename != "") {
          self::$log_errors = ini_get('log_errors');
          ini_set('log_errors', 'On');
          self::$error_log = ini_get('error_log');
          ini_set('error_log', self::$logfilename);
        }
      self::$sys_err_log = $OnOff_err_log;
    }
    //
    if(self::$lastTime == 0) self::$lastTime = microtime(true);  //save timestamp
  }

 /*
  * aktivate catch Error and Exceptions
  */
  public static function catchError()
  {
    register_shutdown_function(array(__CLASS__,'shutDownHandle'), true); 
  }
  
 /*
  * save the loginformation in a buffer or logfile, no output
  */
  public static function save(/** $var1, $var2, .. **/) 
  {
    if (!self::$debug_on_off OR !self::$switchOn) return;
    $argv = func_get_args();
    $backtrace = debug_backtrace();
    self::$recbuf .= self::recArg($argv,$backtrace);
  }

 /*
  * general output for saved and current debug-infos on display or logfile
  */
  public static function write(/** $var1, $var2, .. **/) 
  {
    if (!self::$debug_on_off OR !self::$switchOn) return;  //do nothing 
    $argv = func_get_args();
    $backtrace = debug_backtrace();
    self::displayAndLog($argv,$backtrace);  
  }
  
 /*
  * general output for saved and current debug-infos on display or logfile
  * if $condition == true
  * if 
  */
  public static function writeIf($condition = null/** $var1, $var2, .. **/) 
  {
    if (!self::$debug_on_off OR !self::$switchOn) return;  //do nothing
    if($condition) {    
      $argv = func_get_args();
      array_shift($argv);
      $backtrace = debug_backtrace();
      self::displayAndLog($argv,$backtrace); 
    }
    else {
      //clear all Debug-Infos from save
      self::$recbuf = "";  
    }
  }

  
  //write with color red
  public static function wrc(/** $var1, $var2, .. **/) 
  {
    if (!self::$debug_on_off  OR !self::$switchOn) return;  //do nothing 
    $defaultFormat = self::$trHeadStyle;
    self::setTitleColor('#f44');
    $argv = func_get_args();
    $backtrace = debug_backtrace();
    self::displayAndLog($argv,$backtrace); 
    self::$trHeadStyle = $defaultFormat;    
  }
  
 /*
  * general output for saved and current debug-infos on display or logfile
  * display text in <pre>-tags
  */
  public static function writePre(/** $var1, $var2, .. **/) 
  {
    if (!self::$debug_on_off OR !self::$switchOn) return;  //do nothing 
    $argv = func_get_args();
    $backtrace = debug_backtrace();
    self::displayAndLog($argv,$backtrace,array('pre'=>1));  
  }

 /*
  * display strings as hex
  */
  public static function writeHex(/** $var1, $var2, .. **/) 
  {
    if (!self::$debug_on_off OR !self::$switchOn) return;  //do nothing 
    $argv = func_get_args();
    $backtrace = debug_backtrace();
    self::displayAndLog($argv,$backtrace,array('pre'=>1,'hex'=>1));  
  }

 /*
  * display strings with unicode characters as \u{fc}
  */
  public static function writeUni(/** $var1, $var2, .. **/) 
  {
    if (!self::$debug_on_off OR !self::$switchOn) return;  //do nothing 
    $argv = func_get_args();
    $backtrace = debug_backtrace();
    self::displayAndLog($argv,$backtrace,array('pre' => 1,'uni'=>1));  
  }
  
  
  //get the loginformation from buffer and delete buffer
  //return html-table (up to V1.95 getClean() )
  public static function html(/** $var1, $var2, .. **/) 
  {
    $argv = func_get_args();
    $backtrace = debug_backtrace();
    self::$recbuf .= self::recArg($argv,$backtrace);  //save current info
    $content = self::$recbuf;
    self::$recbuf = "";
    return $content;
  }


  /*
  * general output like write a output for saved and actual debug-infos on display or logfile 
  * $condition : if condition ist true or null then write and throw a exception, in the other case do nothing
  * if $condition is int, write and stop at the $condition call
  * if $condition is int and 0 or <0 do nothing (also no  decrement stopCounter)
  */
  public static function stop($condition = null /**, $var1, $var2, .. **/) 
  {
    if (!self::$debug_on_off OR !self::$switchOn) return null;  //do nothing
    $argv = func_get_args();
    $exceptionMessage = "";
    if($condition !== null) {
      array_shift($argv);
      if(is_bool($condition)) {
        if(!$condition) return false; //$condition is false
        $exceptionMessage = "condition boolean";
      }
      else {
        $maxZyk = (int) $condition;
        if($maxZyk <= 0) return false;  //0 und negative Zahlen haben keine Wirkung
        if(self::$stopCounter === null) self::$stopCounter = $maxZyk -1;
        --self::$stopCounter;
        if(self::$stopCounter >= 0) return false;
        $exceptionMessage = "condition max.cycle=".$maxZyk;
      }
    }
    $backtrace = debug_backtrace();
    self::displayAndLog($argv,$backtrace); 
    self::closeLog();
    if(self::$exitOnStop) {
      //exit(); 
      throw new Exception(htmlspecialchars('debug::stop '.$exceptionMessage,ENT_QUOTES,"UTF-8"));  
    }
    return true;
  }

  /*
  * Reset for Stop Counter
  */
  public static function resetStopCounter() 
  {
    self::$stopCounter = null;
  }
  
  
 /*
  * clear internal buffer, deletes all information that have been saved
  * logfile-infos not delete
  */
  public static function clear() 
  {
    self::$recbuf = "";
  }
  
  //Info
  public static function systeminfo() {
    $info = PHP_OS.' PHP '.PHP_VERSION.' ('.(PHP_INT_SIZE * 8).'Bit) ';
    self::write($info);
  }
  
  /*
   * Returns the 32-bit CRC checksum of the argument as a hex string of length 8
   * accept int, float, string, arrays and objects, no resource
   * 
   */
  public static function crc($value) {
    return sprintf('%X',crc32(json_encode($value)));
  }
  
  //sleep $microSeconds, return real count microseconds
  public static function microSleep($microSeconds) {
    $tStart = microtime(true);
    $tEnd = $microSeconds * 1.E-6 + $tStart;
    while( microtime(true)<= $tEnd);
    return (int)((microtime(true) - $tStart)*1000000.);
  }
  
  /*
  * TypeInfo returns a string type of (len,class,resource-typ..)
  */
  public static function TypeInfo($obj, $options = array()) {
    $objType = gettype($obj);
    if(is_string($obj)) {
      $len = strlen($obj);
      $encoding = self::detect_encoding($obj);
      return $objType."(".$len.") ".$encoding;
    }
    if(is_array($obj)) return  $objType."(".count($obj).")";
    if(is_object($obj)) {
      if($obj instanceof Countable) $objlenght = count($obj);
      elseif($obj instanceof DOMNodeList) $objlenght = $obj->length;
      else $objlenght = count((array)$obj);
      $objectId = function_exists('spl_object_id') 
        ? "#".spl_object_id($obj)
        : ""
      ; 
      return $objType."(".get_class($obj).")".$objectId." (".$objlenght.")"; //2013
    }
    if(is_resource($obj)) return $objType."(".get_resource_type($obj).")(".(int)$obj.")";
    if((bool)$obj AND var_export($obj,true)==='NULL') {
      //closed Resource
      return "resource(?)(".(int)$obj.")";
    }    
    return $objType;
  }
   
  
  /*
   * return a string as hexadecimal like '\x61\x62..'
   */
  public static function strhex($s){
    return $s != '' ? '\\x'.implode('\\x',str_split(bin2hex($s),2)) : '';
  }

  /*
   * return char for Unicode-Format U+20ac (U+0000..U+3FFF)
   * false if error
   */
  public static function UniDecode($strUplus){
    $str = preg_replace("/^U\+([0-9A-F]{4,5}$)/i", "&#x\\1;", $strUplus); //4-5 Hexzahlen nach U+ rausfiltern
    return $str != $strUplus ? html_entity_decode($str, ENT_QUOTES, 'UTF-8') : false;
   }


 /*
  * return PHP unicode string Format '\u{20ac}\A' for all multibyte chars of string
  * for non utf8 chars returns "\xhh"
  */
  public static function strToUnicode($string){
    $ret = "";
    $bytePos = 0;
    while(true){
      $string = substr($string,$bytePos);
      if($string == "") break;
      $char = self::firstChar($string);
      $bytePos = strlen($char);
      if($bytePos == 1){
        if(preg_match('~^[\x21\x23-\x7e]$~',$char)) $ret .= $char;
        else $ret .= '\x'.sprintf("%02x",ord($char));
      }
      else {
        //multibyte
        $utf32char = mb_convert_encoding($char,"UTF-32BE","UTF-8");
        $int32arr = unpack("N",$utf32char);
        $ret .= '\u{'.dechex($int32arr[1]).'}';
      }
    }
    return $ret;
  }
  
 /*
  * get first utf-8 multibyte char or first byte
  * @param string input: valid or invalid utf-8 string
  * @return string, bool false if error (for string = "")
  */  
  public static function firstChar($string){
    $ok = preg_match('/
      ^[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]
      |^[\xE0-\xEF][\x80-\xBF][\x80-\xBF]
      |^[\xC0-\xDF][\x80-\xBF]
      |^./sx',
      $string,
      $match);
    return $ok ? $match[0] : false;      
  }

 /*
  * convert a string with unicode \u{30} or hex code \x30 to string
  * @param string $string
  * @return string
  * example: debug::unicodeToString("a\x42\u{e4}\u{20ac}") -> "aBä€"
  */
  public static function unicodeToString($string){
    return preg_replace_callback(
      '#\\\\(x)([[:xdigit:]]{2})|\\\\u{([[:xdigit:]]{1,5})}#ism',
      function($matches)
        {
          if($matches[1] == 'x') return chr(hexdec($matches[2]));
          return mb_convert_encoding(pack("N",hexdec($matches[3])),"UTF-8","UTF-32BE");
        },
      $string
    );
  }
   
  /*
   * tries to determine the charset of string
   * return p.E. 'UTF-8','UTF-8 BOM','ISO-8859-1','ASCII' 
   * faster as mb_detect_encoding($string,'ASCII, UTF-8, ISO-8859-1',true);
   */
  public static function detect_encoding($string) {
    if( preg_match("/^\xEF\xBB\xBF/",$string)) return 'UTF-8 BOM';
    if( preg_match("//u", $string)) { 
      //valid UTF-8
      if( preg_match("/[\xf0-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/",$string)) return 'UTF-8mb4';
      if( preg_match("/[\xe0-\xef][\x80-\xbf][\x80-\xbf]/",$string)) return 'UTF-8mb3';
      if( preg_match("/[\x80-\xff]/",$string)) return 'UTF-8';
      return 'ASCII';
    } else {
      if(preg_match("/[\x7f-\x9f]/",$string)) {
        //kein ISO/IEC 8859-1
        if(preg_match("/[\x81\x8d\x90\x9d]/",$string)) {
          //kein CP1252
          return "";
        }
        return 'CP1252';
      }
      return 'ISO-8859-1';
    }
    //return false;
  }
  
  //set a new color for background of title
  public static function setTitleColor($backgroundColor) {
    self::$trHeadStyle = preg_replace('/background-color:.+;/','background:'.$backgroundColor.';',self::$trHeadStyle);
  }
  
  //set style for gd-resource
  public static function setImgStyle($style){
    self::$gdStyle = $style;
  }
  
  //set $gdOutputFormat
  //param Ident : "png" or "jpg"
  public static function setGdOutputFormat($Ident){
    self::$gdOutputFormat = preg_match('~^\.?j*~i',$Ident) ? 'jpg' : 'png';
  }
 
 /*
  * Activate the log depending on the contents of the file
  * If content "1" Log is enabled, else if content "0" disabled
  * If param is bool, permanent activ/deaktive with true/false
  * Default filePath: __DIR__.'/debug.switch'
  */  
  public static function switchLog($filePath = NULL){
    if(is_bool($filePath)) {
      self::$switchOn = $filePath;
      return;
    }
    if(empty($filePath)) {
      $backtrace = debug_backtrace();
      $calledDir = isset($backtrace[0]['file']) 
        ? dirname($backtrace[0]['file'])."/"
        : "" ;
      $filePath = $calledDir.'debug.switch';
    }
    if(is_readable($filePath)) {
      self::$switchOn = (bool)(int)file_get_contents($filePath);
    }
    else {
      $message = "Switch-File ".$filePath." is not readable";
      trigger_error(htmlspecialchars($message,ENT_QUOTES,"UTF-8"), E_USER_WARNING);
    }
  }
  
 /*
  * Delete a Logfile set with debug::log("filename")
  * debug::write after deleteLogFile will display on screen
  */  
  public static function deleteLogFile(){
    if(self::$logfilename != "") {
      if(file_exists(self::$logfilename)) {
        unlink(self::$logfilename);
      }
      self::$logfilename = "";
    }
  }

 /*
  * Delete last Segment from Logfile
  * @return bool true if logfile cut, false if not 
  */  
  public static function deleteLastLogFileSegment(){
    if(self::$logfilename != "" AND file_exists(self::$logfilename)) {
      $logContent = file_get_contents(self::$logfilename);
      $posLastMark = strrpos($logContent,self::$logMark);
      if($posLastMark > 1) {
        file_put_contents(self::$logfilename, substr($logContent,0,$posLastMark)); 
        return true;
      }
    }
    return false;
  }

  
 /*
  * get the current LogFileName
  */
  public static function getLogFileName(){
    return self::$logfilename;
  }
  
 /*
  * return true if debug mode is on
  */
  public static function isOn(){
    return self::$debug_on_off;
  }
  
  //Start stopwatch
  public static function setTimestamp($index = 0){
    self::$timeStamps[$index] = microtime(true);  
  }

  //get Time in µs from start stowatch to now
  //return int time µs or false if index not valid
  public static function getTimestampDiff($index = 0){
    $stop = microtime(true);
    return isset(self::$timeStamps[$index]) 
      ? (int)(($stop-self::$timeStamps[$index]) * 1000000)
      : false;
  }
  
 /* 
  * get Infos for headers sent
  * return array (
     'headerSent' => false,
     'fileName' => "",
     'line' => 0,
     'output_buffering' => "",
     'bytes output buffer' => false,
    )
  */
  public static function headerInfo() {
    $sent = headers_sent($fileName, $line);
    return array(
      'headerSent' => $sent,
      'fileName'   => $fileName,
      'line'       => $line,
      'output_buffering' => ini_get('output_buffering'),
      'bytes output buffer' => ob_get_length(),
      'headers' => headers_list(),
    );
      
  }  
  
  //is header sent
  public static function isHeaderSent() {
    return headers_sent(); 
  }  

  
 /*
  * non public functions
  */

 /*
  * special function modify a string from var_export 
  */ 
  protected static function modifyVarExport($code,$addPlaceHolder = true, $options = []) {
    $code = substr($code,1,-1);  //remove single quotes
    //remove exotic illustration ."\0". from var_export
    $code = str_replace("' . \"\\0\" . '",chr(0),$code);  
    $search = array("\\'",'"',"\r","\n","\t");
    $replace = array("'",'\"','\r','\n','\t',);
    $code = str_replace($search, $replace, $code);
    if(isset($options['hex'])) {
      $regEx = '/[\x00-\xff]/sS';
    }
    else {
      if(preg_match("//u",$code)) {
        $regEx = '/[\p{C}]/usS';
      }
      else {
        $regEx = '/[\x00-\x1f\x7f-\xff]/sS';
      }
    }
    $code = preg_replace_callback(
      $regEx,
      function($m) use ($addPlaceHolder) {
        $hx = '\\x'.implode('\\x',str_split(bin2hex($m[0]),2));
        return $addPlaceHolder ? ('{~+~}'.$hx.'{~-~}') : $hx;
        }, 
      $code
    );
    $code = preg_replace('~ (?= )~',"\\x20",$code); //more as 2 spaces to \x20
    return '"'.$code.'"';
  }

  protected static function displayAndLog($argv = null, $backtrace = null,$options=array())
  {
    if($argv !== null){  
      self::$recbuf .= self::recArg($argv,$backtrace,$options);  //save current info
    }  
    if(self::$logfilename == "") 
    { //empty logfilename -> display
      echo self::$recbuf;
      if(self::$real_time_output) {
         echo (str_repeat(' ',4096))."\r";
         flush();
      }
    }
    else 
    { //write logfile
      file_put_contents(self::$logfilename,self::$recbuf,FILE_APPEND);
    }
    self::$recbuf = "";
  }

  protected static function backtraceInfo($backtrace)
  {
    $backtraceKeys = array('class','object','type','function');  //'file','args','type'
    $cutAfterChars = ':(';  //strings from arguments and objects cut after this chars 
    $fromFile = $info = ""; 
    foreach($backtrace as $i => $bi) {
      if($i == 0) 
      {
        $fromFile = isset($bi['file']) ? $bi['file'] : '';
        $info .= " ".$bi['class'].$bi['type'].$bi['function'];
        $info .= ' "'.basename($fromFile).(isset($bi['line']) ? '" Line '.$bi['line'] : '"');
      }
      else
      {
        $fromFileNew = isset($bi['file']) ? $bi['file'] : '';
        if($fromFileNew != '' && $fromFileNew != $fromFile) {
          $fromFile = $fromFileNew;
          $fromFileInfo = '"'.basename($fromFile).'"';
        }
        else {
          $fromFileInfo = '';
        }
        if(array_key_exists('line',$bi)) $info .= " <=".$fromFileInfo." Line ".$bi["line"];
        //$info .= " &lt;= Line ".$bi["line"];
        foreach($backtraceKeys as $k) 
        {
          if(isset($bi[$k])) 
          { 
            $info .= " ".(is_string($bi[$k]) ? $bi[$k] : '{'.get_class($bi[$k]).'}');
            if($k == 'function') 
            {
              $args = "(";
              if(isset($bi['args']))
              {
                foreach($bi['args'] as $arg) {
                  if(is_scalar($arg)) {  //01.08.2014
                    $param = var_export($arg,true);
                    if(is_string($arg)) {
                      $param = self::modifyVarExport($param,false);
                      if(strlen($param) > 16) {
                        //cut string parameter fix 16
                        $param = substr($param,0,16).'.."';
                      }
                    }
                    $args .= ($bi[$k] === 'include') 
                      ? $param
                      :preg_replace('/['.$cutAfterChars.'].*/s','',$param).", "
                    ;
                  } 
                  else {
                    $args .= gettype($arg).',';
                  }
                }
                $info .= rtrim($args,", ").")";
              }
            }
          }
        }
      }
    } 
    return $info;
  }
  
 /*
  * rec arguments
  * option 'pre'=>1 im pre-style
  * option 'hex' => 1 
  * option 'uni' => 1 (writeUni)  
  */
  protected static function recArg($argv, $backtrace, $option = array()) {
    //$btel = array('class','object','type','function');  //'file','args','type'

    $recadd = '<table style="'.self::$tableStyle.'">'."\r\n".
      '<tr style="'.self::$trHeadStyle.'">'."\r\n".
      '<td style="'.self::$trHeadStyle.'" colspan="3"><b>';
    $microtime_float = microtime(true);
    //times
    $recadd .= " [".date("d.m.Y H:i:s",(int)$microtime_float).",".sprintf("%03d", (int)(fmod($microtime_float,1) * 1000))."]";
    if(self::$lastTime > 0) {
      $diff_microsec = $microtime_float-self::$lastTime;
      if($diff_microsec >= 1.0) $recadd .= "[+". sprintf('%1.3F',$diff_microsec)." s]";
      elseif($diff_microsec >= 0.010) $recadd .= "[+".(int)($diff_microsec*1000)." ms]";
      else $recadd .= "[+".(int)($diff_microsec*1000000)." &mu;s]";
    }
    self::$lastTime = $microtime_float;
    //mem
    $recadd .= '('.(int)(memory_get_usage(false)/1024). 'k/'.(int)(memory_get_peak_usage(false)/1024).'k)'; //kByte
    //backtraceInfo
    $recadd .= self::esc(self::backtraceInfo($backtrace));
    $recadd .= "</b></td></tr>"."\r\n";
    //vars
    foreach($argv as $k => $arg){
      $recadd .= '<tr><td style="width:'.self::$col1width.';'.self::$tdStyle.'"><b> '.
        $k.'</b></td><td style="width:'.self::$col2width.';'.self::$tdStyle.'">';
      $pre = is_object($arg) || is_array($arg) || (is_string($arg) && isset($option['pre'])) 
        ? '<pre style="display:inline">' 
        : ""
      ;
      $typeInfo = self::TypeInfo($arg, $option);
      $recadd .= $typeInfo.'</td><td style="'.self::$tdStyle.'">'.$pre;
      if(is_int($arg)) {
        $t = $arg." [".strtoupper(sprintf("%08x", $arg))."h]";
      }
      elseif(is_string($arg)) {
        if(isset($option['hex'])) {
          $arg = implode("\r",str_split(self::strhex($arg),64));
        }
        elseif(isset($option['uni'])){
          $arg = self::strToUnicode($arg);
        }
        if(empty($option['pre'])) {
          $t = $arg !== "" ? substr($arg,0,self::$stringCut) : "";  //"" for php < 7.0
          $t = var_export($t, true);
          
          if(self::$showSpecialChars) {
            $t = self::modifyVarExport($t);
          }
        }
        else {
          $t = $arg;
        }
      }
      elseif(strncmp($typeInfo,'resource(gd)',12) === 0 OR
        strncmp($typeInfo,'object(GdImage)',15) === 0) {
        $attribute = 'style="'.self::$gdStyle.'"';
        ob_start();
        if(self::$gdOutputFormat == 'jpg') {
          $imgOk = @imagejpeg($arg,NULL,85);
          $t = '<img src="data:image/jpeg;base64,';
        }
        else {
          $imgOk = @imagepng($arg);
          $t = '<img src="data:image/png;base64,';
        }
        if($imgOk) {
          $t .= base64_encode(ob_get_clean()).
            '" '. $attribute .' />';
          $t .= " ".imagesx($arg)." x ".imagesy($arg)." px";  
        }
        else {
          ob_get_clean();
          $t = "Error creating image";          
        }
        if($errorArr = error_get_last()){
          $t .= ' (Error '.$errorArr['message'].')';
          error_clear_last();
        }

      }
      elseif(strncmp($typeInfo,'resource(Socket)',16) === 0 
        AND function_exists('socket_last_error')
        AND ($sockLastErr = socket_last_error($arg)) != 0
      ) {
          $t = "Error ".$sockLastErr.": ".socket_strerror($sockLastErr);
          socket_clear_error();
      }
      else {
        if($arg instanceof SimpleXMLElement) {
          //$t = $arg->asXML();
          $t = self::formatOutput($arg);
          if(!preg_match("/<.+>/",$t)) {
            $t = var_export($arg,true);
            $r = preg_match("/state\((.*)\)/s",$t,$match);
            if($r) $t = $match[1];
          }
        }
        elseif ($arg instanceof DOMElement) {
          //neu 19.6.2013
          $t = $arg->ownerDocument->saveXML($arg);
        }
        elseif ($arg instanceof DOMDocument) {
          //neu 19.6.2013
          $arg->formatOutput = true;
          $t = $arg->saveXML();
        }
        elseif ($arg instanceof DOMNodeList) {
          //experimental
          $t = "array(\r\n";
                    
          foreach($arg as $curNode) {
            $el = "  ";
            if($curNode instanceof DOMElement) $el .= "DOMElement::";
            $t .= $el."'".$curNode->ownerDocument->saveXML($curNode)."'\r\n";
          }
          $t .= ")";
        }

        elseif (is_array($arg) OR is_object($arg)) {
          $s = print_r($arg,true);
          $t = strpos($s," *RECURSION*") ? $s : var_export($arg,true);
        }
        else {
          $t = var_export($arg,true);
        }  
        $t = str_ireplace('stdClass::__set_state','(object)',$t);  //ab V1.8
        $t = str_replace("' . \"\\0\" . '",chr(0),$t);  //Exot ."\0". entfernen
        //Filter Output
        if(self::$showSpecialChars) {
          $t = preg_replace_callback(
            "/=> ('.*?'),\n/s",
            function($m) use($option){  //ex 'self::cb1'
              return '=> '.self::modifyVarExport($m[1], true, $option).",\n"; 
            }, 
            $t
          );
        }
      }
      if(strncmp($typeInfo,'resource(gd)',12) === 0 OR
        strncmp($typeInfo,'object(GdImage)',15) === 0) {
        $recadd .= $t."</td></tr>";
      }
      else {
        $recadd .= str_replace(
          array('{~+~}','{~-~}'),
          array('<span style="background:#ccc;margin:1px;">','</span>'),
          self::esc($t)
          );
        $recadd .= ($pre ? '</pre>' : '')."</td></tr>";
      }
    }
    $recadd .= "</table>"."\r\n";
    return $recadd;
  }
  
  protected static function closeLog() {
    if(self::$logfilename != "") {
      file_put_contents(self::$logfilename,str_replace("\n[","<br>[",file_get_contents(self::$logfilename)));
      if( !self::$log_file_append) file_put_contents(self::$logfilename,'</body></html>',FILE_APPEND);
    }
    self::$logfilename = '';
  }
  
  //Escape
  protected static function esc($s){
    return htmlspecialchars($s, ENT_QUOTES |ENT_IGNORE, 'UTF-8');
  }
  
  private static function formatOutput($xml) 
  {
    $xml_str = $xml->asXML();
    if(strpos($xml_str,"<?") === false) {
      return $xml;
    }
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml_str);
    return $dom->saveXML();
  }

  //creates a temporary filename from the current time
  //return. Name p.E. "log20161123095906_9065.html"
  private static function tmpFileName($extension = ".html"){
    $ms = sprintf("%04d", (int)(10000 * fmod(microtime(true),1.0)));
    $fileName = "log".date("YmdHis")."_".$ms.$extension;
    return $fileName;  
  }
  
 /*
  * Get a Name for Error-Type
  * @param int $type
  * @return string
  */
  public static function getErrorTypName($type)
  {
    $names = array(
      E_ERROR   => 'E_ERROR',
      E_PARSE   => 'E_PARSE',
      E_WARNING => 'E_WARNING',
      E_NOTICE  => 'E_NOTICE',
      E_STRICT => 'E_STRICT',
      E_USER_ERROR => 'E_USER_ERROR',
      E_USER_NOTICE => 'E_USER_NOTICE',
      E_USER_WARNING => 'E_USER_WARNING',
    );
    $type = (int)$type;
    return array_key_exists($type, $names) ? $names[$type] : ("Error ".$type);    
  }
  
  public static function shutDownHandle($flag = false)
  {
    $errors = error_get_last();
    if(empty($errors) OR $flag !== true) return;
    echo '<br>';
    self::setTitleColor('#f44');
    $backtrace = array(
      array(
        'file' => $errors['file'],
        'line' => $errors['line'],
        'class' => "",
        'type' => "",
        'function' => "",
      )
    );
    //$backtrace = debug_backtrace();
    $message = self::getErrorTypName($errors['type'])." :".$errors['message'];
    self::displayAndLog(array($message),$backtrace); 
  }

 /*
  * detect UTF encodings 
  * @param string $string
  * @param int $checkLen : uses a maximum of checkLen bytes for analysis
  * @return string : UTF-8, UTF-8_BOM, UTF-16, UTF-16_BOM 
  *  UTF-32, UTF-32_BOM, Other
  */
  public static function detectUTFencoding($string, $checkLen = 64)
  {
    $checks = [
      'UTF-32LE' => ['~^\xff\xfe\x00\x00~','~^[^\x00].\x00\x00$~'],
      'UTF-32BE' => ['~^\x00\x00\xfe\xff~','~^\x00\x00.[^\x00]$~'],
      'UTF-16LE' => ['~^\xff\xfe~','~^[^\x00]\x00[^\x00]\x00$~'],
      'UTF-16BE' => ['~^\xfe\xff~','~^\x00[^\x00]\x00[^\x00]$~'],
      'UTF-8' => ['~^\xef\xbb\xbf~','~^[^\x00][^\x00][^\x00][^\x00]$~']
    ];
    foreach($checks as $key => $regEx){ //check BOM
      if(preg_match($regEx[0],$string)) return $key.'_BOM';
    }
    $subStr = substr($string,0,$checkLen);
    $arr = str_split($subStr,4);
    $count = 0;
    $coding = "";
    foreach($checks as $key => $regEx){
      $filterArr = preg_grep($regEx[1],$arr);
      $curCount = $filterArr ? count($filterArr) : 0;
      if($count > $curCount) continue;
      $count = $curCount;
      $coding = $key;
    }
    if($coding !== 'UTF-8' OR preg_match('//u',$subStr)) return $coding;
    return 'Other';
  }

}