<?php
require_once("constantVars.php");
$databaseName = DBNAME;
$tableName = 'offers';

class KnowledgeBase{
  public $patterns = array();
  public $patternResponseGroups = array();
  public $allRowsOriginal = array();
  public $allRowsCurrent = array();
}

$knowledgeBase = new KnowledgeBase();

//open database
$dbc = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME)
    or die('Error connection to MySQL server');

////////////////////////////////////////////////////////    
// First query grabs everyting ////////////////////////
$query = "SELECT * FROM response AS r, pattern AS p, patternResponse AS pr WHERE r.patternResponseID = pr.patternResponseID AND p.patternResponseID = pr.patternResponseID ORDER BY p.priority DESC;";

$result = mysqli_query($dbc, $query)
	or die('Error querying database. 1 \n');

while($row = mysqli_fetch_array($result)){
     array_push($knowledgeBase->allRowsOriginal, $row);
}
//make the $allRows variable that will be changed when nessecary
$knowledgeBase->allRows = $knowledgeBase->allRowsOriginal;


////////////////////////////////////////////////////
//Seconds query grabs just the patternResponse groups
$query = "SELECT * FROM patternResponse ORDER BY patternResponseID;";

$result = mysqli_query($dbc, $query)
	or die('Error querying database. 2 \n');

$tempPatternResponseGroups = array();
while($row = mysqli_fetch_array($result)){
     array_push($tempPatternResponseGroups, $row);
}

/////////////////////////////////////////////////////
//Third query graps all patterns
$query = "SELECT * FROM pattern ORDER BY priority DESC;";

$result = mysqli_query($dbc, $query)
	or die('Error querying database. 2 \n');

while($row = mysqli_fetch_array($result)){
     array_push($knowledgeBase->patterns, $row);
}

//close database
mysqli_close($dbc);




// Create array of initial structure of groups
$tempArray = array();
foreach($tempPatternResponseGroups as $PRG){
  $tempArray['patternResponseID'] = $PRG['patternResponseID'];
  $tempArray['patterns'] = array();
  $tempArray['responses'] = array();
  
  array_push($knowledgeBase->patternResponseGroups, $tempArray);
  
  $tempArray = array();
}

//TODO: ATM the patternResponseGroups maping to the knowledge base with an  ID  number and the $index is messign up...

//Now add all of the pattens and resposnes to their respective groups
$index = 0;
foreach($tempPatternResponseGroups as $PRG){
  foreach($knowledgeBase->allRows as $row){
    if($row['patternResponseID'] == $PRG['patternResponseID']){
      array_push($knowledgeBase->patternResponseGroups[$index]['patterns'], $row['regex']);
      array_push($knowledgeBase->patternResponseGroups[$index]['responses'], $row['responseString']);
    }
  }
  $index +=1;

}

////////////////////////////////////////////////////////////////
///////////////DATA BASE PARSING ABOVE//////////////////////////
////////////////////////////////////////////////////////////////

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
