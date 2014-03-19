<?php
require_once("constantVars.php");
require_once("knowledgeBase.php");

//Generate Knowledge Base.
function generateKnowledgeBase(){
	$knowledgeBase = new KnowledgeBase();


	//open database
	$dbc = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME)
		or die('Error connection to MySQL server');

	////////////////////////////////////////////////////////    
	// First query grabs everyting ////////////////////////
	$query = "SELECT * FROM response AS r, pattern AS p, patternResponse AS pr
				WHERE r.patternResponseID = pr.patternResponseID
				AND p.patternResponseID = pr.patternResponseID 
				ORDER BY p.priority DESC;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 1 \n');

	while($row = mysqli_fetch_array($result)){
		array_push($knowledgeBase->allRows, $row);
	}


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




	//TODO: Change the below structure into an object.
	// Only use arrays for actual array and objects for attribute.

	// Create array of initial structure of groups
	$tempArray = array();
	foreach($tempPatternResponseGroups as $PRG){
		$tempArray['patternResponseID'] = $PRG['patternResponseID'];
		$tempArray['patterns'] = array();
		$tempArray['responses'] = array();
		$tempArray['name'] = $PRG['name'];
		$tempArray['command'] = $PRG['command'];
		$tempArray['priority'] = $PRG['priority'];

		array_push($knowledgeBase->patternResponseGroups, $tempArray);

		$tempArray = array();
	}


	//Now add all of the pattens and resposnes to their respective groups
	$index = 0;
	foreach($tempPatternResponseGroups as $PRG){
		foreach($knowledgeBase->allRows as $row){
			if($row['patternResponseID'] == $PRG['patternResponseID']){
				array_push($knowledgeBase->patternResponseGroups[$index]['patterns'], 
					$row['regex']);
				array_push($knowledgeBase->patternResponseGroups[$index]['responses'], 
					$row['responseString']);
			}
		}
		$index +=1;
	}
	return $knowledgeBase;
}
?>
