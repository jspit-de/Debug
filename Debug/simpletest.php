<?php
require 'class.debug.php';
$string = "äöü\n";
$int = 5;
debug::write($string,$int);
