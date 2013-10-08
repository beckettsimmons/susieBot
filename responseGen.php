<?php
require_once('generateKnowledgeBase.php');

class UserData
{
	public $userID;
	//patterns for each user, so that topics changes indepentdently
	public $patterns =  array();
	public $responseNumber = 0; //how many responses and questions have been sent.
	public $email = ""; // if we ever get their email, it goes here.
    // etc.
	function __construct($userID, $patternList){
		$this->userID = $userID;
		$this->patterns = $patternList;
		print "Created new User\n";
	}
}


function getResponse ($stanza, $userDataList){
	global $knowledgeBase;
	global $allRows;
	//$tempUserData = new UserData();
	$response = "default response";
	$breakFlag = false;//flag to break out of the second loop on pattern match
	$patternResponseID = -1;

	
	//if an old user isn't found, create new profile data
	if(!isset($knowledgeBase->userBase[$stanza->from])){
		$knowledgeBase->userBase[$stanza->from] = 
			new UserData($stanza->from,$knowledgeBase->patterns);
	}
	
	//now search for regex matches through all of the pre-sorted by priotiy patterns.
	foreach($knowledgeBase->userBase[$stanza->from]->patterns as $pattern){
		// the /'s that are before and after the regex are mandatory php syntax evidently.
		// the trailing i make the regex case insensitive
		if(preg_match("/" . $pattern['regex'] . "/i", $stanza->body)){
			$patternResponseID = $pattern['patternResponseID'];
			break;
		}
	}
	//If we find a match then find all of it's corresponding responses
	foreach($knowledgeBase->patternResponseGroups as $knowledgeBit){
		if($knowledgeBit['patternResponseID'] == $patternResponseID){
			$response =  $knowledgeBit['responses']
				[rand(0,sizeof($knowledgeBit['responses'])-1)];
			$breakFlag = True;
			break;
		}
	}
	return $response;
}
?>
