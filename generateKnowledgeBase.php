<?php
require_once("constantVars.php");
require_once("knowledgeBase.php");

//Generate Knowledge Base.
function generateKnowledgeBase(){
	$knowledgeBase = new KnowledgeBase();
	$tempPatternResponseGroups = array();
	$tempChangeByResponse = array();
	$tempChangeByPatternResponse = array();

	//open database
	$dbc = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME)
		or die('Error connection to MySQL server');

	/**
	 *  First query grabs an inner join of the patternResponse, pattern,
	 *  and respose tables.
	 */
	$query = "SELECT * FROM response AS r, pattern AS p, patternResponse AS pr
				WHERE r.patternResponseID = pr.patternResponseID
				AND p.patternResponseID = pr.patternResponseID
				ORDER BY p.priority DESC;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 1 \n');

	while($row = mysqli_fetch_array($result)){
		array_push($knowledgeBase->allRows, $row);
	}


	/**
	 * Second query grabs just the patternResponse table.
	 */
	$query = "SELECT * FROM patternResponse ORDER BY patternResponseID;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 2 \n');

	while($row = mysqli_fetch_array($result)){
		array_push($tempPatternResponseGroups, $row);
	}

	/**
	 * Third query grabs all of the pattern table and orders by priority.
	 */
	$query = "SELECT * FROM pattern ORDER BY priority DESC;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 3 \n');

	while($row = mysqli_fetch_array($result)){
		array_push($knowledgeBase->patterns, $row);
	}

	/**
	 * Forth query grabs everything in the changeContextByResponse table.
	 */
	$query = "SELECT * FROM changeContextByResponse;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 4 \n');

	while($row = mysqli_fetch_array($result)){
		array_push($tempChangeByResponse, $row);
	}


	/**
	 * Fith query grabs everything in the changeContextByPatternResponse table.
	 */
	$query = "SELECT * FROM changeContextByPatternResponse;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 5 \n');

	while($row = mysqli_fetch_array($result)){
		array_push($tempChangeByPatternResponse, $row);
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
		$tempArray['contextID'] = $PRG['contextID'];
		$tempArray['changeContext'] = array();

		array_push($knowledgeBase->patternResponseGroups, $tempArray);

		$tempArray = array();
	}


	//Now add all of the pattens and resposnes to their respective groups
	//TODO: Maybe rename all these silly acronyms...
	foreach($knowledgeBase->patternResponseGroups as &$PRG){
	
		// Add change by pattern response data here.
		foreach($tempChangeByPatternResponse as $CBPR){
			if($CBPR['contextID'] == $PRG['contextID']){

				$tempCBPRArray = array();
				// Build list of context changes for current pattern response.
				foreach($tempChangeByPatternResponse as $TCBPR){
					if($TCBPR['patternResponseID'] == $CBPR['patternResponseID']){
						$tempCBPRArray = array();
						array_push(
							$tempCBPRArray,
							$CBPR
						);
					}
				}

				$PRG['changeContext'] = $tempCBPRArray;
			}
		}
	}
	// Passing the patternResponseGroups by reference. 
	// Maybe not the best practice?
	foreach($knowledgeBase->patternResponseGroups as &$PRG){
		foreach($knowledgeBase->allRows as $row){
			if($row['patternResponseID'] == $PRG['patternResponseID']){
				// Add the pattern regex accordingly.
				array_push(
					$PRG['patterns'],
					$row['regex']
				);


				$tempCBRArray = array();
				// Get list of context changes for the current response.
				foreach($tempChangeByResponse as $CBR){
					if($CBR['responseID'] == $row['responseID']){
						$tempCBRArray = array();
						array_push(
							$tempCBRArray,
							$CBR
						);
					}
				}
				//If a response has a context change, added it to the end.
				//If there was no info just append a blank array.
				array_push(
					$PRG['responses'],
					(object) array(
						'responseString' => $row['responseString'],
						'changeContext' => $tempCBRArray
					)
				);
			}
		}
	}


	return $knowledgeBase;
}
?>
