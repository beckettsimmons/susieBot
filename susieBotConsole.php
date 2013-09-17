<?php
// This is a console forms of susie bot for testing purposes.

require_once 'responseGen.php';

class Stanza{
  public $from = 1;
}

$userDataList = array();

$input = "";
while($input != "exit"){
  $input = readline("You: ");
  $stanza = new Stanza();
  echo getResponse($stanza, $userDataList) . "\n";
}


?>
