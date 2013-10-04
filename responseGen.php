<?php
require_once('generateKnowledgeBase.php');

class UserData
{
    public $clientID;
    public $responseNumber = 0; //how many responses and questions have been sent.
    public $email = ""; // if we ever get their email, it goes here.

    // method declaration
    public function displayVar() {
        echo $this->var;
    }
}


function getResponse ($stanza, $userDataList){
    global $knowledgeBase;
    global $allRows;
    $tempUserData = new UserData();
    $response = "default response";
    $breakFlag = false;//flag to break out of the second loop on pattern match
    
    
    //now search for regex matches through all of the pre-sorted by priotiy patterns.
    foreach($knowledgeBase->patterns as $pattern){
      
    
      // the /'s that are before and after the regex are mandatory php syntax evidently.
      // the trailing i make the regex case insensitive.
      if(preg_match("/" . $pattern['regex'] . "/i", $stanza->body)){
	  
	  //If we find a match then find all of it's corresponding responses via the knowledge Base
	  foreach($knowledgeBase->patternResponseGroups as $knowledgeBit){
	    if($knowledgeBit['patternResponseID'] == $pattern['patternResponseID']){
	      $response =  $knowledgeBit['responses'][rand(0,sizeof($knowledgeBit['responses'])-1)];
	      $breakFlag = True;
	      break;
	    }
	  }
	  if($breakFlag == True){ break;}
	  echo "Whoops something went wrong in the getResponse function and a corresponding knowledgeBit was not found..";
	}
      
    }
    
    return $response;
}
?>
