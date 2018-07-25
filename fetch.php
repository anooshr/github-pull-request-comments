<?php

// put your github token here (get it from https://github.com/settings/tokens)
$GITHUB_OAUTH2_TOKEN="";
$GITHUB_REPO_URL="";

// we need a PR id 
$pr_id = $argv[1]; 

// sanity check 
if (!$pr_id) {
	echo "Need a PR id."; 
	exit -1; 
}

// get the comments via cURL
$url = "https://api.github.com/repos/" . $GITHUB_REPO_URL . "/pulls/" . $pr_id . "/commits"; 
// add auth 
$url .= "?access_token=" . $GITHUB_OAUTH2_TOKEN; 

// get it 
ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)'); 
$json = file_get_contents($url);
// its a json
$commits = json_decode($json); // object form 

// sanity check 
if (!$commits){
	echo "Unable to fetch commits."; 
	exit -1; 
}

// lets put in a nice format 
foreach ($commits as $commit) {
	echo "* " . prettify($commit->commit->message) . " (" . $commit->author->login . ")"; 
	echo PHP_EOL; 
}


/**
 * Clean up multi line commits.
 */
function prettify($text){
	// is this multi line? 
	$lines = explode(PHP_EOL, $text); 
	$commit_line = []; 

	// if its a single line, return as-is
	if(count($lines) == 1) {
		return $lines[0]; 
	}
	
	// clean up 
	foreach ($lines as $line) {
		// remove all lines that start with "Merge"
		if(substr($line, 0, 5) === "Merge"){
			// ignore it 
			continue; 
		}
		// clean up whitespace 
		$line = trim($line); 

		// skip empty 
		if (empty($line)){
			continue;
		}

		// otherwise push it back together 
		$commit_line[] = $line; 
	}
	// done 
	return implode(' ', $commit_line);
}