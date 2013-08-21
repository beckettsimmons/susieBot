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
    
    // first find out if we alreqady have data on the user
    foreach($userDataList as $user){
        if(!is_null($user)){
            if($user->clientID== $stanza->from){
                $tempUserData = $user;
                break;
            }
        }
    }
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
    return $response;
}
?>