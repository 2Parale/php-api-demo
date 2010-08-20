<?php

function add_token($token, $secret, $network) {
  global $DB;

  $query = "INSERT INTO installations (token, secret, network) VALUES ('$token', '$secret', '$network')";
  $result = $DB->Execute($query) or die("Error in query: $query. " . $DB->ErrorMsg());
}

function find_token($token) {
  global $DB, $SECRET;

  $result = $DB->Execute("SELECT * FROM installations");
  while ($array = $result->FetchRow()) {
    $possible_public = md5($SECRET."-".$array['secret']);
    if ($possible_public == $token) {
      return $array;
    }
  }
  return null;
}

?>
