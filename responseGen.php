<?

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