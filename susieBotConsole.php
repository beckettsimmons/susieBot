<?php
// This is a console forms of susie bot for testing purposes.

require_once 'responseGen.php';
require_once("userData.php");
require_once("generateKnowledgeBase.php");

$knowledgeBase = generateKnowledgeBase();

class Stanza{
	public $from = 11;
}

$userDataList = array();

$input = "";
while($input != "exit"){
	$input = readline("You  : ");
	$stanza = new Stanza();
	$stanza->body = $input;
	echo "Susie: " . $knowledgeBase->getResponse($stanza) . "\n";
}


?>
