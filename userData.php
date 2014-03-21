<?php

/**
 * This object contains all of the unique and dynamic user data.
 * Data is stored here so that it can be change independently from other users.
 *
 * @property int $userID
 * @property array $patterns The pattern for the user.
 * @property array $patternResponseGroups Processed pattern response groups.
 * @property int $responseNumber The number of responses Susie has made..
 * @property string $email Just incase we ever get that data...
 */
class UserData
{
	public $userID;
	public $patterns =  array();
	public $patternResponseGroups = array();
	public $responseNumber = 0;
	public $email = "";

	/**
	 * Method initializes the user's data.
	 *
	 * @param int $userID User id of the new user.
	 * @param array $patternList To pass list of patterns to use for new user.
	 * @param array patternResponseGroupsList Again, pass a non defualt list.
	 */
	function __construct($userID, $patternList, $patternResponseGroupsList){
		$this->userID = $userID;
		$this->patterns = $patternList;
		$this->patternResponseGroups = $patternResponseGroupsList;
		print "Created new User\n";
	}

	/**
	 * Method returns the index of a specific patternID.
	 *
	 * @param int $patternID
	 * @return int $index
	 */
	function getPatternIndex($patternID){
		$index = 0;
		foreach($this->patterns as $pattern){
			if($pattern['patternID'] == $patternID){
				return $index;
			}
			$index+=1;
		}
	}

	/**
	 * Set the prioty of a pattern.
	 * This is a very low down method, proabably should only be locally used.
	 *
	 * @param int $patternID
	 * @param int $priority the new prioty to set the pattern at.
	 */
	// TODO: Method needs to be renamed to setPatternPriority().
	function setPriority($patternID, $priority){
		$index = $this->getPatternIndex($patternID);
		$this->patterns[$index]['priority'] = $priority;
		
		//Resort the list.
		foreach ($this->patterns as $key => $pattern) {
			$priorities[$key]  = $pattern['priority'];
		}
		array_multisort($priorities, SORT_DESC, SORT_NUMERIC,
						$this->patterns);
	}

	/**
	 * Sets the pattern priority of a whole pattern response group.
	 *
	 * @param int $patternResponseID
	 * @param int $priority The new priority.
	 */
	// TODO: Again rename this to be more specific.
	function setGroupPriority($patternResponseID, $priority){
		//find the right group first.
		foreach($this->patterns as $pattern){
			if($pattern['patternResponseID'] == $patternResponseID){
				$this->setPriority($pattern['patternID'], $priority);
			}
		}
	}

	/**
	 * Set the context of a user data session.
	 *
	 * @param int $contextID Which context to change.
	 * @param int $truePriority An actually number.
	 * @param string $relativePriority For relative placement.
	 *		Excepts, TOP, MIDDLE, BOTTOM.
	 */
	function setContext($contextID, $truePriority, $relativePriority){
		// Decide which priority to use. Prefer rel over true.
		if($relativePriority == NULL){
			$priority = $truePriority;
		}else{
			//Calculate the relative priority below!
			//TODO: Maybe we shouldn't assume presorted patterns list...
			if($relativePriority=='TOP'){
				$priority = $this->patterns[0]['priority'] + 1;
			}
			if($relativePriority=='MIDDLE'){
				//TODO: Make a better estimation if this will be kept!
				$arraySize = sizeof($this->patterns)/2;
				$priority = $this->patterns[$arraySize]['priority'] - 1;
			}
			if($relativePriority=='BOTTOM'){
				$arraySize = sizeof($this->patterns);
				$priority = $this->patterns[$arraySize-1]['priority'] - 1;
			}
		}

		foreach($this->patternResponseGroups as $PRG){
			if($PRG['contextID'] == $contextID){
				$this->setGroupPriority($PRG['patternResponseID'], $priority);
			}
		
		}
	}
}
?>
