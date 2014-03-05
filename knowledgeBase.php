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
		//TODO: Maybe create a local reference to the selected user?
		
		//Search for pattern match.
		foreach($this->userBase[$stanza->from]->patterns as $pattern){
			// the /'s that are mandatory php syntax evidently.
			// the trailing i make the regex case insensitive
			if(preg_match("/" . $pattern['regex'] . "/i", $stanza->body)){
				//If match, set the patternResponseID for later use.
				$patternResponseID = $pattern['patternResponseID'];
				break;
			}
		}

		//If pattern found, select appropriate responsei
		if($patternResponseID != -1){
			foreach($this->patternResponseGroups as $knowledgeBit){
				if($knowledgeBit['patternResponseID'] == $patternResponseID){
					//Execute any php commands that could be used in making a resopnse
					eval($knowledgeBit['command']);
					$randomIndex =
						rand(0,sizeof($knowledgeBit['responses'])-1);
					$tempResponse = $knowledgeBit['responses']
								[$randomIndex]->responseString;

					eval("\$response = \"$tempResponse \";");
					
					//Now change the context if nessecary.
					$arraySize = sizeof(
						$knowledgeBit['responses'][$randomIndex]
							->changeContext
					);
					if($arraySize != 0){
						//TODO: Loop through whole array of changeContext
						$this->userBase[$stanza->from]->setContext(
							$knowledgeBit['responses'][$randomIndex]
								->changeContext[0]['contextID'],
							$knowledgeBit['responses'][$randomIndex]
								->changeContext[0]['newPriority']
						);
					}

					$breakFlag = True;
					break;
				}
			}
		}
		return $response;
	}

	
}
