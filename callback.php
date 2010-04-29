<?php
require 'config.php';

session_start();

$network = $_SESSION['network'];

$consumer = new HTTP_OAuth_Consumer($KEY, $SECRET, $_SESSION['token'], $_SESSION['token_secret']);
$consumer->getAccessToken("http://".$network."/oauth/access_token", $_GET['oauth_verifier']);

add_token($consumer->getToken(), $consumer->getTokenSecret(), $_SESSION['network']);

header("Location: http://".$network."/oauth_clients/show?token=". $consumer->getToken());
?>

