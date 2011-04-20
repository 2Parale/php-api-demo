<?php
require '2pphp/2performant.php';
require 'adodb5/adodb.inc.php';

require 'common-funcs.php';

/* OAuth stuff */
$KEY    = '6cxYDKQ6fGSBcd6kq4KF ';
$SECRET = 'mAukD3yyWANeIkAYJMqTHWKfUOxVqCiCHg9MjKj3';

// Location of callback.php
$CALLBACK = "http://localhost/2p-demo/callback.php";

/* DB Stuff */
$DB = NewADOConnection('mysql');
$DB->Connect('localhost', 'root', 'aiurea', '2pdemo'); 

?>
