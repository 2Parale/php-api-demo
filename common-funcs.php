<?php

function add_token($token, $secret, $network) {
  global $DB;

  $query = "INSERT INTO installations (token, secret, network) VALUES ('$token', '$secret', '$network')";
  $result = $DB->Execute($query) or die("Error in query: $query. " . $DB->ErrorMsg());
}

function find_token($token) {
  global $DB;

  $result = $DB->Execute("SELECT * FROM installations WHERE token = ?", $token);
  return $result->FetchRow();
}

?>
