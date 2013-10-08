<?php
//Define the Knowledge Base
class KnowledgeBase{
	public $patterns = array();
	public $patternResponseGroups = array();
	public $allRowsOriginal = array();
	public $allRowsCurrent = array();
	public $userBase = array();

	function createNewUser($userID, $patternList = null){
		if($patternList == null){ $patternList = $this->patterns;}
		$this->userBase[$userID] = 
			new UserData($userID, $patternList);
	}

	function getResponse ($stanza){
		//$tempUserData = new UserData();
		$response = "default response";
		$patternResponseID = -1;

		
		//Create new profile data if nessecary
		if(!isset($this->userBase[$stanza->from])){
			$this->createNewUser($stanza->from);
		}
		
		//Search for pattern match.
		foreach($this->userBase[$stanza->from]->patterns as $pattern){
			// the /'s that are mandatory php syntax evidently.
			// the trailing i make the regex case insensitive
			if(preg_match("/" . $pattern['regex'] . "/i", $stanza->body)){
				$patternResponseID = $pattern['patternResponseID'];
				break;
			}
		}

		//If pattern found, select appropriate responsei
		if($patternResponseID != -1){
			foreach($this->patternResponseGroups as $knowledgeBit){
				if($knowledgeBit['patternResponseID'] == $patternResponseID){
					$response =  $knowledgeBit['responses']
						[rand(0,sizeof($knowledgeBit['responses'])-1)];
					$breakFlag = True;
					break;
				}
			}
		}
		return $response;
	}

}

?>
