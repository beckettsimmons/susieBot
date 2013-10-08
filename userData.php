<?php
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
?>
