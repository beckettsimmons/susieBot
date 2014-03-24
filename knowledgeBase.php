<?php
/**
 * The knowledge base is used to store all of the data nessecary for Susie.
 *
 * @property array $patterns Just the patterns.
 * @property array $patternResponseGroups Processed pattern response groups.
 * @property array $allRows All pattern response group rows from database.
 * @property array $userBase An associative array of user IDs with user data.
 */
class KnowledgeBase{
	public $patterns = array();
	public $patternResponseGroups = array();
	public $allRows = array();
	//The knowledgebase contains an array of userbases.
	//Not the opposite because a userbase's KB can change.
	public $userBase = array();

	/**
	 * Method creates a new user profile and adds it to the knowledge base.
	 *
	 * @param int $userID User id of the new user.
	 * @param array $patternList The list of patterns to use for new user.
	 * @param array patternResponseGroupsList Pass a non defualt list.
	 */
	function createNewUser(
		$userID,
		$patternList = null,
		$patternResponseGroupsList=null
	){
		// Initialization.
		if($patternList == null){
			$patternList = $this->patterns;
		}
		if($patternResponseGroupsList == null){
			$patternResponseGroupsList = $this->patternResponseGroups;
		}

		$this->userBase[$userID] =
			new UserData($userID, $patternList, $patternResponseGroupsList);
	}

	/**
	 * Method returns a string response based on the user data.
	 *
	 * @param JAXLMessageObj $stanza The JAXL message object received.
	 * @returns string $response The response to be sent to user.
	 */
	function getResponse ($stanza){
		$response = "default response";
		$patternResponseID = -1;
		
		//Create new user profile data if nessecary.
		if(!isset($this->userBase[$stanza->from])){
			$this->createNewUser($stanza->from);
		}
		//TODO: Maybe create a local reference to the selected user?
		// Just to shorten up the syntax. And also because we don't modify it.
		
		//Search for pattern match.
		foreach($this->userBase[$stanza->from]->patterns as $pattern){
			// The /'s that are mandatory php syntax evidently.
			// The trailing 'i' makes the regex case insensitive.
			if(preg_match("/" . $pattern->regex . "/i", $stanza->body)){
				//If match, set the patternResponseID for later use.
				$patternResponseID = $pattern->patternResponseID;
				break;
			}
		}

		//If pattern found, select appropriate response.
		if($patternResponseID != -1){
			foreach($this->patternResponseGroups as $knowledgeBit){
				if($knowledgeBit->patternResponseID == $patternResponseID){
					//Execute any php commands that may be needed.
					eval($knowledgeBit->command);
					
					// TODO: Maybe in the future, prioritize responses?
					// TODO: Feature that prevents repeated responses.
					// Going to pick a random response index.
					$randomIndex =
						rand(0,sizeof($knowledgeBit->responses)-1);
					$tempResponse = $knowledgeBit->responses
								[$randomIndex]->responseString;

					eval("\$response = \"$tempResponse \";");
					
					//Now change the context if nessecary.
					//Change by pattern response.
					$arraySize = sizeof($knowledgeBit->changeContext);
					if($arraySize != 0){
						foreach($knowledgeBit->changeContext
									as $changeContext){
							$this->userBase[$stanza->from]->setContext(
								$changeContext->contextID,
								$changeContext->newPriority,
								$changeContext->relativePriority
							);
						}
					}
					//Now for change by response.
					$arraySize = sizeof(
						$knowledgeBit->responses[$randomIndex]
							->changeContext
					);
					if($arraySize != 0){
						foreach($knowledgeBit->responses[$randomIndex]
									->changeContext as $changeContext){
							
							$this->userBase[$stanza->from]->setContext(
								$changeContext->contextID,
								$changeContext->newPriority,
								$changeContext->relativePriority
							);
						}
					}

					$breakFlag = True;
					break;
				} // if
			} //foreach
		} //if
		return $response;
	} // getResponse()
}
