<?php

function add_token($token, $secret, $network) {
  global $DB, $SECRET;

  $public_token = md5($SECRET."-".$secret);
  $query = "INSERT INTO installations (token, secret, public_token, network) VALUES ('$token', '$secret', '$public_token', '$network')";
  $result = $DB->Execute($query) or die("Error in query: $query. " . $DB->ErrorMsg());
}

function find_token($token) {
  global $DB;

  $result = $DB->Execute("SELECT * FROM installations WHERE token = ?", $token);
  return $result->FetchRow();
}

function find_public_token($public_token) {
  global $DB;

  $result = $DB->Execute("SELECT * FROM installations WHERE public_token = ?", $public_token);
  return $result->FetchRow();
}
?>
