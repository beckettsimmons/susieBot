<?php
class UserData
{
	public $userID;
	//patterns for each user, so that topics changes indepentdently
	public $patterns =  array();
	public $patternResponseGroups = array();
	public $responseNumber = 0; //how many responses and questions have been sent.
	public $email = ""; // if we ever get their email, it goes here.
    // etc.
	function __construct($userID, $patternList, $patternResponseGroupsList){
		$this->userID = $userID;
		$this->patterns = $patternList;
		$this->patternResponseGroups = $patternResponseGroupsList;
		print "Created new User\n";
	}

	//get the index of a pattern
	function getPatternIndex($patternID){
		$index = 0;
		foreach($this->patterns as $pattern){
			if($pattern['patternID'] == $patternID){return $index;}
			$index+=1;
		}
	}

	//Set pattern piority
	function setPriority($patternID, $priority){
		$index = $this->getPatternIndex($patternID);
		$this->patterns[$index]['priority'] = $priority;
		
		//Resort the list.
		foreach ($this->patterns as $key => $row) {
			    $priorities[$key]  = $row['priority'];
		}
		array_multisort($priorities, SORT_DESC, SORT_NUMERIC, 
						$this->patterns);

	}

	//Set priority of a whole patternResponse group
	function setGroupPriority($patternResponseID, $priority){
		//find the right group first.
		foreach($this->patterns as $pattern){
			if($pattern['patternResponseID'] == $patternResponseID){
				$this->setPriority($pattern['patternID'], $priority);
			}
		}
	}
}
?>
