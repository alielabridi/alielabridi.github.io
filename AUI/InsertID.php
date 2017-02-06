<?php
/*
      CREATED BY ALI ELABRIDI
      FOR THE HONOR CLASS OF DATABASE SYSTEM
      TO DEMONSTRATE THE USE WS POLICIES LIKE IN A 
      REST ENVIRONMENT
      SUPERVISOR: Pr Youssra Chtouki

*/
/*uploading the policies present in the json file*/
$policies = json_decode(file_get_contents("policies.json"));
/*validating whether it is a json file or not*/
function isValidJSON($str) {
   json_decode($str);
   return json_last_error() == JSON_ERROR_NONE;
}
/*validating the synthax of the json file and check whether all the elements present in the policie are sent in the query*/
function hasValidSynthax($decoded_query,$policies){
	$i = 0;
	foreach ($decoded_query as $key => $value) {
		if($key != $policies->restPolicies[0]->Elems[$i++])
			return false;
	}
	return true;
}
/*check whether the authentifications present in the policy are correct with a md5 hashing of the password*/
function checkAuthentification($decoded_query,$policies){
	return ($policies->restPolicies[1]->UsernameAccess == $decoded_query->UsernameAccess && $policies->restPolicies[2]->MD5Password == md5($decoded_query->MD5Password));
}
/*getting query and transforming json into PHP vars*/
$json_query = file_get_contents("php://input");
$decoded_query = json_decode($json_query);

/*check the validity of the first policy*/
if (isValidJSON($json_query) && hasValidSynthax($decoded_query,$policies)){
		if (checkAuthentification($decoded_query,$policies))
		{
			/*create a new object that matches the structure of the json database of AUI ids*/
			$newParamInserted = new stdClass();
			$newParamInserted->ID = $decoded_query->ID;
			$newParamInserted->Firstname = $decoded_query->Firstname;
			$newParamInserted->Lastname = $decoded_query->Lastname;
			$jsonFileStorage = json_decode(file_get_contents("AUI_IDs.json"));
			/*push the object into the structure of json*/
			array_push($jsonFileStorage,$newParamInserted);
			$newJson = json_encode($jsonFileStorage);
			/*store it into json*/
			file_put_contents('AUI_IDs.json', $newJson);
			/*throw a successful response code*/
			http_response_code(200);
		}
		else 
			http_response_code(401);
}
else
	http_response_code(406);

?>