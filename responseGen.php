<?php
require_once("constantVars.php");
$databaseName = DBNAME;
$tableName = 'offers';

//open database
$dbc = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME)
    or die('Error connection to MySQL server');

// First query grabs everyting
$query = "SELECT * FROM response AS r, pattern AS p, patternResponse AS pr WHERE r.patternResponseID = pr.patternResponseID AND p.patternResponseID = pr.patternResponseID;";

$result = mysqli_query($dbc, $query)
	or die('Error querying database. 1 \n');

$allRows = array();
while($row = mysqli_fetch_array($result)){
     array_push($allRows, $row);
}



//Seconds query grabs just the patternResponse groups
$query = "SELECT * FROM patternResponse ORDER BY patternResponseID;";

$result = mysqli_query($dbc, $query)
	or die('Error querying database. 2 \n');

$patternResponseGroups = array();
while($row = mysqli_fetch_array($result)){
     array_push($patternResponseGroups, $row);
}


//close database
mysqli_close($dbc);

// Create array of initial groups
$knowledgeBase = array();
$tempArray = array();
foreach($patternResponseGroups as $PRG){
  array_push($tempArray, $PRG['patternResponseID']);
  array_push($tempArray, array());
  array_push($tempArray, array());
  
  array_push($knowledgeBase, $tempArray);
  
  $tempArray = array();
}

//TODO: ATM the patternResonseGropus maping to the knowledge base with an  ID  number and the $index is messign up...

//Now add all of the pattens and resposnes to groups
$index = 0;
foreach($patternResponseGroups as $PRG){
  foreach($allRows as $row){
    if($row['patternResponseID'] == $PRG['patternResponseID']){
      array_push($knowledgeBase[$index][1], $row['regex']);
      array_push($knowledgeBase[$index][2], $row['string']);
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
    
    // first find out if we alreqady have data on the user
    /*foreach($userDataList as $user){
        if(!is_null($user)){
            if($user->clientID== $stanza->from){ // if we get a match then we want to update the user data and get a response.
                $tempUserData = $user;
                //echo "got here";
                
                switch ($tempUserData->responseNumber) {
                    case 0:
                        $response = "1";
                        break;
                    case 1:
                        $response = "2";
                        break;
                    case 2:
                        $response = "3";
                        break;
                }
                $tempUserData->responseNumber+=1;
                
                $user = $tempUserData;
    
    
    
    
                break;
            }
        }
    }*/
    foreach($knowledgeBase as $knowledgeBit){
    // the /'s that are before and after the regex are mandatory php syntax evidently.
    // the trailing i make the regex case insensitive.
      foreach($knowledgeBit[1] as $regex){
      
	if(preg_match("/" . $regex . "/i", $stanza->body)){
	  $response =  $knowledgeBit[2][rand(0,sizeof($knowledgeBit[2]))];
	  break;
	}
      }
    }
    
    
    return $response;
}
?>