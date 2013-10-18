<?php
//Define the Knowledge Base
class KnowledgeBase{
	public $patterns = array();
	public $patternResponseGroups = array();
	public $allRows = array();
	public $userBase = array();

	function createNewUser($userID, $patternList = null, $patternResponseGroupsList=null){
		if($patternList == null){ $patternList = $this->patterns;}
		if($patternResponseGroupsList == null){
			$patternResponseGroupsList = $this->patternResponseGroups;
		}
		$this->userBase[$userID] = 
			new UserData($userID, $patternList, $patternResponseGroupsList);
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
				$this->userBase[$stanza->from]->
						setGroupPriority($pattern['patternResponseID'], -99);
				break;
			}
		}

		//If pattern found, select appropriate responsei
		if($patternResponseID != -1){
			foreach($this->patternResponseGroups as $knowledgeBit){
				if($knowledgeBit['patternResponseID'] == $patternResponseID){
					//Execute any php commands that could be used in making a resopnse
					eval($knowledgeBit['command']);

					$tempResponse = $knowledgeBit['responses']
								[rand(0,sizeof($knowledgeBit['responses'])-1)] ;

					eval("\$response = \"$tempResponse \";");
					 
					$breakFlag = True;
					break;
				}
			}
		}
		return $response;
	}

	
}

?>
