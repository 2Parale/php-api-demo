<?php
require '2pphp/2performant.php';
require 'adodb5/adodb.inc.php';

require 'common-funcs.php';

/* OAuth stuff */
$KEY    = 'qJ63a4vm3U35cMtSUERQ';
$SECRET = 'HHaLZDKywGzIGFRsomEhC85TJWeDY0r4NDOaiI5O';

// Location of callback.php
$CALLBACK = "http://localhost/2p-demo/callback.php";

/* DB Stuff */
$DB = NewADOConnection('mysql');
$DB->Connect('localhost', 'root', 'aiurea', '2pdemo'); 

?>
