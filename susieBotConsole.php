<?php
// This is a console forms of susie bot for testing purposes.

require_once("userData.php");
require_once("generateKnowledgeBase.php");

$knowledgeBase = generateKnowledgeBase();

class Stanza{
	public $from = 11;
}
$userDataList = array();


//lines for allowing the 'thought' loop
stream_set_blocking(STDIN, false);
$time = microtime(true);
$line = '';
$prompt = 'You: ';


echo $prompt;
//Starting a primative 'though engine'.
while(true){
	$c = fgetc(STDIN);

	if (microtime(true) - $time > 5){
		$time = microtime(true);
	}

	if ($c !== false){
		if ($c != "\n")
			$line .= $c;
		else{
			if ($line == "exit")
				break;
			else{
				$stanza = new Stanza();
				$stanza->body = $line;
				echo "Susie: ". $knowledgeBase->getResponse($stanza)."\n";
			}
			echo $prompt;
			$line = '';
		}
	}
	sleep(1);
}





?>
