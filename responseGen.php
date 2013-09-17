<?php
require_once("constantVars.php");
$databaseName = DBNAME;
$tableName = 'offers';

//open database
$dbc = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME)
    or die('Error connection to MySQL server');

// insert data into the sers table
$query = "SELECT * FROM response";

$result = mysqli_query($dbc, $query)
	or die('Error querying database.');

// $rows = array();
// 
// while($row = mysqli_fetch_array($result)){
//     array_push($rows, $row);
// }
//close database

mysqli_close($dbc);

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
    $tempUserData = new UserData();
    $response = "default response";
    
    // first find out if we alreqady have data on the user
    foreach($userDataList as $user){
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
    }
    
    return $response;
}
?>