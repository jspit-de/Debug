<?php
//error_reporting(-1);  //dev
//last update 2017-10-20
error_reporting(E_ALL ^ (E_WARNING | E_USER_WARNING));
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

require __DIR__.'/../class/class.debug.php';
require __DIR__.'/../class/phpcheck.php';
$t = new PHPcheck();
//Reset Filter to enable html output
$t->setResultFilter("html");
//Outputvariante Form
//$t->setOutputVariant("Form");

//echo $t->getHtmlHeader('phpcheck class.debug');
//version
$t->start('check versions info');
$info = $t->getClassVersion("debug");
$t->check($info, !empty($info));

$t->startOutput('debug::write integer');
$var = 1;
debug::write($var);
$t->checkOutput('Line '.(__LINE__-1).',integer,1h');
//echo $t->getTable();
//echo $results['result'];


$t->startOutput('debug::write float');
$var = 1.1;
debug::write($var);
$t->checkOutput('Line '.(__LINE__-1).',1.1');

$t->startOutput('debug::write ASCII string');
$var = "ASCII-String";
debug::write($var);
$t->checkOutput('string(12),ASCII');

$t->startOutput('debug::write string, integer');
$var = 6789;
debug::write('Ein Integer:',$var);
$t->checkOutput(6789);

$t->startOutput('debug::write UTF8 string');
$var = "Umlaute äöü";
debug::write($var);
$t->checkOutput('string(14),UTF,äöü');

$t->startOutput('debug::write Binary String');
$var = chr(0x7F).chr(3)."\t\r\n";
debug::write($var);
$t->checkOutput('\x7f,\x03,\t,\r,\n');

$t->startOutput('debug::write Binary String with \0');
$var = chr(0)."abc"."\0";
debug::write($var);
$t->checkOutput('\x00,abc');

$t->startOutput('debug::write String with Backlash');
$var = "a".chr(0x5C)."b";  //'a\b' 3 chars
debug::write($var);
$t->checkOutput('string(3),'."a".chr(0x5C).chr(0x5C).'b');

$t->startOutput('debug::write String with Backlash');
$var = "a\\b";  //'a\b' 3 chars
debug::write($var);
$t->checkOutput('string(3),'."a".chr(0x5C).chr(0x5C).'b');

$t->startOutput('debug::write String with Single Quotes');
$var = "test's"; 
debug::write($var);
$t->checkOutput('string(6),'."test&#039;s");

$t->startOutput('debug::write String with Double Quotes');
$var = "test \"hochkomma\""; 
debug::write($var);
$t->checkOutput('string(16),test,\&quot;hochkomma');

$t->startOutput('debug::writePre Text');
$sqlString = "SELECT
  id, eventdate, eventdesc
FROM
  tab
LIMIT
  100;
"; 
debug::writePre($sqlString);
//check if 2 blank spaces in front of 100
$t->checkOutput('SELECT,  100');   

$t->startOutput('debug::writeHex');
$string = "012";
debug::writeHex($string);
$t->checkOutput('\x30\x31\x32');   


//arrays


$t->startOutput('debug::write array');
$var = array("a" => 1, "b" => 5.6, "string" => "text");
debug::write($var);
$t->checkOutput('array(3)');

$t->startOutput('debug::write Array with Recursion');
$var = array("a","b");
$var['c'] = & $var;
debug::write($var);
$t->checkOutput('array');

$t->startOutput('debug::write object');
class data{
  public $p1 = 23;
  public $p2 = "test_p2";
}
$var = new data;
debug::write($var);
$t->checkOutput('data::');

$t->startOutput('debug::write object with *RECURSION*');
$var->c = $var;
debug::write($var);
$t->checkOutput('data,RECURSION');

$t->startOutput('debug::write date-time object');
$date = new DateTime();
debug::write($date);
$t->checkOutput(array('date',date("Y-m-d")));

$t->startOutput('debug::write Simple XML');
$strXml = "<root><div>45</div></root>";
$var = simplexml_load_string($strXml);
debug::write($var);
$t->checkOutput('45');

$t->startOutput('debug::write DOMDocument');
$str = "<!DOCTYPE html><div>test3</div>";
$doc = new DOMDocument();
$doc->loadHTML($str);
debug::write($doc);
$t->checkOutput('DOMDocument,&lt;div,test3,/div');

$t->startOutput('debug::write DOMNodeList');
$str = "<!DOCTYPE html><div>test4</div>";
$doc = new DOMDocument();
$doc->loadHTML($str);
$nodeList = $doc->getElementsByTagName('div');
debug::write($nodeList);
$t->checkOutput('DOMNodeList,&lt;div,test4,/div');

$t->startOutput('debug::write resource(gd)');
$img = imagecreate(100 , 50);
ImageColorAllocate($img, 0, 255, 0);
debug::write($img);
$t->checkOutput('resource(gd)');

$t->startOutput('debug::write inside function');
function testFct1($par1,$par2) {
  debug::write($par1,$par2);
}
testFct1("abc",78);
$t->checkOutput('testFct1(,abc,78');

$t->startOutput('debug::write inside object method');
class testClass1{
  private $dateTimeObj;
  public function __construct(DateTime $par){
    debug::write('$par,$this',$par,$this);
  }
}
$myclass = new testClass1(new datetime);
$t->checkOutput('__construct');

$t->startOutput('debug::write inside closure');
$testFct2 = function ($par1,$par2) {
  debug::write($par1,$par2);
};
$testFct2("text\r\n",95);
$t->checkOutput('closure,text,95');

$t->startOutput('debug::write resource(gd) with error');
$img = imagecreate(50 , 50);
debug::write($img);
$t->checkOutput('resource(gd)');

$t->startOutput('debug::save ');
debug::save("Save into buffer, Output with next write");
debug::write();
$t->checkOutput('Debug::save,Save into');

$t->startOutput('debug::clear');
debug::save("Save Info, do not know if it is needed");
debug::clear(); // delete all save-infos !
debug::write('First information is not output');
$t->checkOutput('Debug::write,not output');

$t->startOutput('debug::stop at once');
try{
  debug::stop(true,"Stop at once");
} catch(exception $e){
  echo "stop_exception";
}
$t->checkOutput('Stop at once,stop_exception');

$t->startOutput('debug::stop at 4. cycle');
try{
  for($i=1;$i<10;$i++){
    debug::stop(4,"Stop at cycle",$i);
  }
} catch(exception $e){
  echo "stop_exception at cycle ".$i;
}
$t->checkOutput('stop_exception at cycle 4');

$t->startOutput('write with red background');
debug::wrc('important message');
$t->checkOutput('wrc');

$t->start('get debug info as html');
$result = debug::html('a info');
$t->checkContains($result,'a info');

$t->start('strhex:');
$result = debug::strhex("test\r\n");
$t->checkEqual($result, '\x74\x65\x73\x74\x0d\x0a'); 

$t->startOutput('systeminfo');
debug::systeminfo();
$t->checkOutput();

$t->start('microSleep: Sleep number of Mikroseconds');
$tStart = microtime(true);
$result = debug::microSleep(10000);
$t->checkEqual($result,10000,'',20); 

$t->startOutput('debug::switchLog - enable');
//Simulate extern task to enable output
file_put_contents(__DIR__.'/debug.switch',"1");
debug::switchLog(); //refresh Switch
debug::write("This information is shown");
$t->checkOutput('Debug::write,This information is shown');

$t->startOutput('debug::switchLog - disable');
//Simulate extern task to disable output
file_put_contents(__DIR__.'/debug.switch',"0");
debug::switchLog();
debug::write("Info is not shown");
$t->checkOutput('');

$t->startOutput('debug::switchLog - enable permanent');
debug::switchLog(true); //enable,ignore extern file
debug::write("This information is also shown");
$t->checkOutput('Debug::write,also');

$t->start('debug::log("TMP") - log into tempfile');
debug::log("TMP");
debug::write('LOGINFO');
$tmpFileName = debug::getLogFileName();
$logContent = file_get_contents($tmpFileName);  //read file
$testOk = strpos($logContent,"LOGINFO") > 0;
$t->check($logContent, $testOk);

$t->start('Delete temp. Logfile');
debug::deleteLogFile();
$t->check($tmpFileName, !file_exists($tmpFileName));

$t->start('Get 32-bit CRC of String, Object, Array');
$result = debug::crc('abc');
$t->checkEqual($result, '2E5A7DD1');

$t->start('Get TypeInfo of String Variable');
$result = debug::TypeInfo('abc');
$t->checkContains($result, 'string,3');

$t->start('Get TypeInfo of Class Instance');
$object = (object)array(1,2);
$result = debug::TypeInfo($object);
$t->checkEqual($result, 'object(stdClass)(2)');

$t->start('Get TypeInfo of Class implements Countable');
class counter implements Countable {
    public function count() {
       return 17; 
    }
}
$object = new counter();
$result = debug::TypeInfo($object);
$t->checkEqual($result, 'object(counter)(17)');

$t->start('Get TypeInfo of DOMNodeList');
$html ='<li>Text1-1</li><li>Text2-2</li>';
$doc = new DOMDocument();
$r = $doc->loadHTML($html);
$nodelist = $doc -> getElementsByTagName("li");
$result = debug::TypeInfo($nodelist);
$t->checkEqual($result, 'object(DOMNodeList)(2)');

$t->start('UniEncode');
$result = debug::UniEncode('A');
$t->checkEqual($result, 'U+0041');

$t->start('UniDecode');
$result = debug::UniDecode('U+0041');
$t->checkEqual($result, 'A');

/*
 * End Tests 
 */

// echo $t->getHtmlFooter();
//output as table
//echo $t->gethtml();
echo (empty($_GET) ? $t->gethtml() : $t->getTotalInfo());

