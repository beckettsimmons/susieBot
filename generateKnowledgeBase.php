<?php
require_once("constantVars.php");
require_once("knowledgeBase.php");

/**
 * Creates a knowledge base by extracting data from database.
 *
 * @return object Returns a knowledge base object.
 */
function generateKnowledgeBase(){
	$knowledgeBase = new KnowledgeBase();
	$tempPatternResponseGroups = array();
	$tempChangeByResponse = array();
	$tempChangeByPatternResponse = array();

	//Create database connection variable.
	$dbc = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME)
		or die('Error connection to MySQL server');
	//TODO What exactly does $allRows do? It should be renamed.
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
		array_push(
			$knowledgeBase->allRows,
			(object) array(
			'responseID' => $row['responseID'],
			'name' => $row['name'],
			'responseString' => $row['responseString'],
			'patternResponseID' => $row['patternResponseID'],
			'patternID' => $row['patternID'],
			'regex' => $row['regex'],
			'priority' => $row['priority'],
			'command' => $row['command'],
			'contextID' => $row['contextID']
			)
		);
	}


	/**
	 * Second query grabs just the patternResponse table.
	 */
	$query = "SELECT * FROM patternResponse ORDER BY patternResponseID;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 2 \n');

	while($row = mysqli_fetch_array($result)){
		array_push(
			$tempPatternResponseGroups, 
			(object) array(
				'patternResponseID' => $row['patternResponseID'],
				'name' => $row['name'],
				'command' => $row['command'],
				'priority' => $row['priority'],
				'contextID' => $row['contextID']
			)
		);
	}

	/**
	 * Third query grabs all of the pattern table and orders by priority.
	 */
	$query = "SELECT * FROM pattern ORDER BY priority DESC;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 3 \n');

	while($row = mysqli_fetch_array($result)){
		array_push(
			$knowledgeBase->patterns,
			(object) array(
				'patternID' => $row['patternID'],
				'name' => $row['name'],
				'regex' => $row['regex'],
				'patternResponseID' => $row['patternResponseID'],
				'priority' => $row['priority']
			)
		);
	}

	/**
	 * Forth query grabs everything in the changeContextByResponse table.
	 */
	$query = "SELECT * FROM changeContextByResponse;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 4 \n');

	while($row = mysqli_fetch_array($result)){
		array_push(
			$tempChangeByResponse,
			(object) array(
				'changeContextByResponseID' =>
					$row['changeContextByResponseID'],
				'contextID' => $row['contextID'],
				'responseID' => $row['responseID'],
				'newPriority' => $row['newPriority'],
				'relativePriority' => $row['relativePriority']
			)
		);
	}


	/**
	 * Fith query grabs everything in the changeContextByPatternResponse table.
	 */
	$query = "SELECT * FROM changeContextByPatternResponse;";

	$result = mysqli_query($dbc, $query)
		or die('Error querying database. 5 \n');

	while($row = mysqli_fetch_array($result)){
		array_push(
			$tempChangeByPatternResponse,
			(object) array(
				'changeContextByPatternResponseID' =>
					$row['changeContextByPatternResponseID'],
				'contextID' => $row['contextID'],
				'patternResponseID' => $row['patternResponseID'],
				'newPriority' => $row['newPriority'],
				'relativePriority' => $row['relativePriority']
			)
		);
	}

	// Close database connection.
	mysqli_close($dbc);




	// Create array of initial structure of groups.
	$tempObject;
	foreach($tempPatternResponseGroups as $PRG){
		$tempObject = (object) array(
			'patternResponseID' => $PRG->patternResponseID,
			'patterns' => array(),
			'responses' => array(),
			'name' => $PRG->name,
			'command' => $PRG->command,
			'priority' => $PRG->priority,
			'contextID' => $PRG->contextID,
			'changeContext' => array()
		);
		array_push($knowledgeBase->patternResponseGroups, $tempObject);
	}


	// Add change by pattern response data here.
	// TODO: Maybe rename all these silly acronyms...
	foreach($knowledgeBase->patternResponseGroups as &$PRG){
		foreach($tempChangeByPatternResponse as $CBPR){
			if($CBPR->contextID == $PRG->contextID){

				$tempCBPRArray = array();
				// Build list of context changes for current pattern response.
				foreach($tempChangeByPatternResponse as $TCBPR){
					if($TCBPR->patternResponseID ==
						$CBPR->patternResponseID
					){
						$tempCBPRArray = array();
						array_push(
							$tempCBPRArray,
							$CBPR
						);
					}
				}

				$PRG->changeContext = $tempCBPRArray;
			}
		}
	}

	//TODO: Passing the patternResponseGroups by reference.
	// Maybe not the best practice?

	// Now add all of the pattens and resposnes to their respective groups.
	foreach($knowledgeBase->patternResponseGroups as &$PRG){
		foreach($knowledgeBase->allRows as $row){
			if($row->patternResponseID == $PRG->patternResponseID){
				// Add the pattern regex accordingly.
				array_push(
					$PRG->patterns,
					$row->regex
				);

				//Now add the response object with context change data.
				$tempCBRArray = array();
				// Get list of context changes for the current response.
				foreach($tempChangeByResponse as $CBR){
					if($CBR->responseID == $row->responseID){
						$tempCBRArray = array();
						array_push(
							$tempCBRArray,
							$CBR
						);
					}
				}
				// If a response has a context change, added it to the end.
				// If there was no info just append a blank array.
				array_push(
					$PRG->responses,
					(object) array(
						'responseString' => $row->responseString,
						'changeContext' => $tempCBRArray
					)
				);
			}
		}
	}

	return $knowledgeBase;
}
?>
