<?php
//error_reporting(-1);  //dev
//last update 2022-10-25 PHP8
//error_reporting(E_ALL ^ (E_WARNING | E_USER_WARNING));
header('Content-Type: text/html; charset=UTF-8');
error_reporting(-1);
ini_set('display_errors', 1);

require __DIR__.'/../class/phpcheck.php';
$t = new PHPcheck();
$t->setOutputOnlyErrors(!empty($_GET['error']));
//Reset Filter to enable html output
$t->setResultFilter("html");
//Outputvariante Form
//$t->setOutputVariant("Form");

$t->start('include class debug');
require __DIR__.'/../class/class.debug.php';
$t->checkEqual(class_exists('debug'), true);

//version
$t->start('check versions info');
$info = $t->getClassVersion("debug");
$t->check($info, $info >= 2.52);

$t->startOutput('debug::write integer');
$var = 1;
debug::write($var);
$t->checkOutput('Line '.(__LINE__-1).',integer,1h');

$t->startOutput('debug::write float');
$var = 1.1;
debug::write($var);
$t->checkOutput('Line '.(__LINE__-1).',1.1');

$t->startOutput('debug::write float result');
$var = 0.7 + 0.1;
debug::write($var);
$t->checkOutput('Line '.(__LINE__-1).',0.799');

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

$t->startOutput('debug::write UTF8 BOM');
$var = "\xEF\xBB\xBFUmlaute äöü";
debug::write($var);
$t->checkOutput('UTF,BOM,äöü');

$t->startOutput('debug::write UTF-8mb3');
$var = "1€";
debug::write($var);
$t->checkOutput('UTF-8mb3,1€');

$t->startOutput('debug::write ISO-String');
$string = "text mit Umlauten äöü";
$iso = mb_convert_encoding($string,"ISO-8859-1","UTF8");
debug::write($iso);
$t->checkOutput('ISO-8859-1');

$t->startOutput('debug::write CP1252-String');
$utf8 = "text mit Umlauten äöü und € Symbol";
$str = mb_convert_encoding($utf8,"CP1252","UTF8");
debug::write($str);
$t->checkOutput('CP1252');

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

$t->startOutput('debug::write String with many Spaces');
$var = "five     spaces"; 
debug::write($var);
$t->checkOutput('five,\x20\x20\x20\x20 ');

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

$t->startOutput('debug::writeUni');
$string = "0123\r\naöäü€ def";
debug::writeUni($string);
$t->checkOutput('0123\\x0d\\x0aa\\u{f6}\\u{e4}\\u{fc}\\u{20ac}\\x20def');

$t->startOutput('debug::writeUni utf8mb4');
$string = "'takriLetterA:𑚀'";
debug::writeUni($string);
$t->checkOutput('takriLetterA:\u{11680}');

$t->startOutput('debug::writeIf: false'); 
debug::save('first Info');
//with condition==false save-Info will be remove
$condition = false;
debug::writeIf($condition,'second Info');
$t->checkEqual($t->getOutput(), "");

$t->startOutput('debug::writeIf: true'); 
debug::save('1.Info');
$condition = true;
debug::writeIf($condition,'2.Info');
$t->checkOutput("save,1.Info,write,2.Info");

//arrays


$t->startOutput('debug::write array');
$var = array("a" => 1, "b" => 5.6, "string" => "text");
debug::write($var);
$t->checkOutput('array(3)');

$t->startOutput('debug::writeHex array');
$var = array("a" => 1, "b" => 5.6, "string" => "text");
debug::writeHex($var);
$t->checkOutput('array(3),\x74');

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
$strXml = '<root><div attr="abc">45</div></root>';
$xml = simplexml_load_string($strXml);
debug::write($xml);
$t->checkOutput('abc,45');

$t->startOutput('debug::write Simple XML attributes');
debug::write($xml->div->attributes());
$t->checkOutput('abc');

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

if(function_exists('socket_create')) {
//
$t->startOutput('debug::write resource(sock) bis PHP7');
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
debug::write($sock);  //ab PHP8 object
$t->checkOutput('(Socket)');
}

//Test grafic resourcen (objects PHP8)
$t->startOutput('debug::write resource(gd)');
$img = imagecreate(100 , 50);
ImageColorAllocate($img, 0, 255, 0);
debug::write($img);
$expectedOutput = intval(PHP_VERSION) >= 8 
  ? 'object(GdImage)'
  :  'resource(gd)'
;
$t->checkOutput($expectedOutput);

$t->startOutput('debug::write resource(gd) with error');
$img = imagecreate(50 , 50);
debug::write($img);
$t->checkOutput($expectedOutput);

$t->startOutput('debug::write truecolor resource gd');
$img = imagecreatetruecolor(50 , 50); 
$green = ImageColorAllocate($img, 0, 255, 0);
$blue = imagecolorallocate($img, 0, 0, 255);
imagefill($img, 0, 0, $green);
imagefilledrectangle($img, 10, 10, 40,40, $blue);
debug::write($img);
$t->checkOutput($expectedOutput);


$t->startOutput('debug::write gd with transparent');
imagecolortransparent($img, $blue);
debug::write($img);
$t->checkOutput($expectedOutput);

$t->startOutput('setImgStyle new background');
debug::setImgStyle('
  max-height:20rem;
  max-width:20rem;
  background-image: repeating-linear-gradient(-45deg, white, #88f 6px);
');
debug::write($img);
$t->checkOutput($expectedOutput);

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

$t->start('set and get timestamps');
debug::setTimestamp(0);
debug::microSleep(10000);
$result = debug::getTimestampDiff(0);
$t->check($result,$result-10000 < 150); 

$t->start('get diff timestamp');
$result = debug::getTimestampDiff(0);
$t->check($result,$result>10000);

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
$t->checkContains($result, 'object(stdClass),(2)');

$t->start('Get TypeInfo of Class implements Countable');
class counter implements Countable {
    #[\ReturnTypeWillChange]
    public function count() {
       return 17; 
    }
}
$object = new counter();
$result = debug::TypeInfo($object);
$t->checkContains($result, 'object(counter),(17)');

$t->start('Get TypeInfo of DOMNodeList');
$html ='<li>Text1-1</li><li>Text2-2</li>';
$doc = new DOMDocument();
$r = $doc->loadHTML($html);
$nodelist = $doc -> getElementsByTagName("li");
$result = debug::TypeInfo($nodelist);
$t->checkContains($result, 'object(DOMNodeList),(2)');

//strToUnicode
$t->start('strToUnicode');
$result = debug::strToUnicode('A');
$t->checkEqual($result, 'A');

$t->start('strToUnicode with " ans space');
$result = debug::strToUnicode(' !"#A~');
$t->checkEqual($result, '\x20!\x22#A~');

$t->start('strToUnicode');
$result = debug::strToUnicode("ä\x80𑚀€");
$t->checkEqual($result, '\u{e4}\x80\u{11680}\u{20ac}');

$t->start('strToUnicode contain UTF8-Fragment');
$string = "ä".substr("€",0,1)."äx";
$result = debug::strToUnicode($string);
$t->checkEqual($result, '\u{e4}\xe2\u{e4}x');

$t->start('strToUnicode contain UTF8-Fragment');
$string = "ä".substr("€",0,2)."äY";
$result = debug::strToUnicode($string);
$t->checkEqual($result, '\u{e4}\xe2\x82\u{e4}Y');

$t->start('strToUnicode check control chars');
$result = debug::strToUnicode("\x03\r\n");
$expected = '\x03\x0d\x0a';
$t->checkEqual($result,$expected);

//unicodeToString
$t->start('unicodeToString');
$code = 'a\x42\u{e4}\u{20ac}';  //not parsed in single quotes
$result = debug::unicodeToString($code);
$t->checkEqual($result, "aBä€");

//UniDecode
$t->start('UniDecode');
$result = debug::UniDecode('U+0041');
$t->checkEqual($result, 'A');

$t->start('UniDecode 4 Byte UTF');
$result = debug::UniDecode('U+1F603');
$t->checkEqual($result, '😃');

$t->start('UniDecode invalid Notation');
$result = debug::UniDecode('U+1F60X');
$t->checkEqual($result, false);


$t->start('detect_encoding ASCII');
$string = "Ein ASCII String";
$result = debug::detect_encoding($string);
$t->checkEqual($result, 'ASCII');

$t->start('detect_encoding UTF-8');
$string = "German ä is a UTF-8 character";
$result = debug::detect_encoding($string);
$t->checkEqual($result, 'UTF-8');

$t->start('detect_encoding UTF-8mb3');
$string = "€ ist a 3 byte UTF-8 character";
$result = debug::detect_encoding($string);
$t->checkEqual($result, 'UTF-8mb3');

//isHeaderSent() + headerInfo()
$t->start('is header sent');
$t->checkEqual(debug::isHeaderSent(), false);

$t->start('headerInfo()');
$result = debug::headerInfo();
$t->check($result, is_array($result));

//detectUTFencoding
$t->start('detect UTF16LE');
$str = 'A Teststring with characters € + äöü';
$test = mb_convert_encoding($str, 'UTF-16LE','UTF-8');
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-16LE');

$t->start('detect UTF16LE with BOM');
$test = "\xff\xfe".mb_convert_encoding($str, 'UTF-16LE','UTF-8');
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-16LE_BOM');

$t->start('detect UTF16BE');
$str = 'A Teststring with characters € + äöü';
$test = mb_convert_encoding($str, 'UTF-16BE','UTF-8');
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-16BE');

$t->start('detect UTF16BE with BOM');
$test = "\xfe\xff".mb_convert_encoding($str, 'UTF-16BE','UTF-8');
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-16BE_BOM');

$t->start('detect UTF32LE');
$str = 'A Teststring with characters € + äöü';
$test = mb_convert_encoding($str, 'UTF-32LE','UTF-8');
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-32LE');

$t->start('detect UTF32LE with BOM');
$test = "\xff\xfe\x00\x00".mb_convert_encoding($str, 'UTF-32LE','UTF-8');
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-32LE_BOM');

$t->start('detect UTF32BE');
$str = 'A Teststring with characters € + äöü';
$test = mb_convert_encoding($str, 'UTF-32BE','UTF-8');
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-32BE');

$t->start('detect UTF32BE with BOM');
$test = "\x00\x00\xfe\xff".mb_convert_encoding($str, 'UTF-32BE','UTF-8');
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-32BE_BOM');

$t->start('detect UTF8');
$str = 'A Teststring with characters € + äöü';
$test = $str;
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-8');

$t->start('detect UTF8 with BOM');
$test = "\xef\xbb\xbf".$str;
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'UTF-8_BOM');

$t->start('detect other');
$str = 'A Teststring with characters € + äöü';
$test = utf8_decode($str);
$result = debug::detectUTFencoding($test);
$t->checkEqual($result, 'Other');

/*
 * End Tests 
 */
//debug::write(debug::headerInfo());
// echo $t->getHtmlFooter();
//output as table
echo $t->gethtml();
