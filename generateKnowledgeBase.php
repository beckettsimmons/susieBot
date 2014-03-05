<?php
require_once("constantVars.php");
require_once("knowledgeBase.php");

//Generate Knowledge Base.
function generateKnowledgeBase(){
	$knowledgeBase = new KnowledgeBase();
	$tempPatternResponseGroups = array();
	$tempChangeByResponse = array();

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


	//close database
	mysqli_close($dbc);




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

		array_push($knowledgeBase->patternResponseGroups, $tempArray);

		$tempArray = array();
	}


	//Now add all of the pattens and resposnes to their respective groups
	//TODO: Why do we have to have an index var here?
	$index = 0;
	foreach($tempPatternResponseGroups as $PRG){
		foreach($knowledgeBase->allRows as $row){
			if($row['patternResponseID'] == $PRG['patternResponseID']){
				// Add the pattern regex accordingly.
				array_push(
					$knowledgeBase->patternResponseGroups[$index]
						['patterns'],
					$row['regex']
				);

				$tempCBRArray = array();
				// Get list yof context changes.
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
				//If there was no info just append a black array.
				array_push(
					$knowledgeBase->patternResponseGroups[$index]['responses'],
					(object) array(
						'responseString' => $row['responseString'],
						'changeContext' => $tempCBRArray
					)
				);

			}
		}
		$index += 1;
	}
	return $knowledgeBase;
}
?>
