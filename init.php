<?php
require 'config.php';

session_start();

$network  = $_GET['network'];

$consumer = new HTTP_OAuth_Consumer($KEY, $SECRET);

// $consumer->getRequestToken(REQUEST TOKEN URL, CALLBACK);
$consumer->getRequestToken("http://".$network."/oauth/request_token", $CALLBACK);

// Store tokens
$_SESSION['token']        = $consumer->getToken();
$_SESSION['token_secret'] = $consumer->getTokenSecret();
$_SESSION['network']      = $network;

$url = $consumer->getAuthorizeUrl('http://'.$network.'/oauth/authorize');
header("Location: $url");

?>

