<?php
header('Content-type: application/json');

function tokenize($str, $sep = "  ", $stripEmpty = true) {

	$tokens = explode($sep, $str);

	// clear empty tokens
	foreach ($tokens as $i => $token) {

		if (! strlen(trim($token))) {

			unset($tokens[$i]);
			continue;

		}

		$tokens[$i] = trim($token);

	}

	$tokens = array_values($tokens);
	$tokens[0] = str_replace(".", "", $tokens[0]);

	return array_values($tokens);

}

function getBlsData($url) {


	$data = array();

	$data['source'] = $url;
	$data['date'] = date("Y-m-d H:i:s");
	$data['time'] = time();

	$in = file_get_contents($url);	
	$lines = explode("\n", $in);

	foreach ($lines as $i => $line) {

		// clear empty lines
		if (! strlen(trim($line))) {
			unset($lines[$i]);
			continue;
		}

		// tokenize
		$lines[$i] = tokenize($line);

	}

	// Step 1 - get the titile
	$data['title'] = '';
	while (count($line = array_shift($lines)) === 1) {
		$data['title'] .= $line[0];
	}
	array_unshift($lines, $line); // put that last one back on ;)


	// Step 2 - combine the next three lines as the group headers
	$data['groups'] = array();
	$g1 = array_shift($lines);
	$g2 = array_shift($lines);
	$g3 = array_shift($lines);
	foreach ($g1 as $i => $g) {
		$data['groups'][$i] = array(
			'text' => @$g1[$i] . " " . @$g2[$i] . " " . @$g3[$i],
			'low' => $g1[$i],
			'high' => $g3[$i]
		);
	}

	// Step 3 - charge through and get the lines of data
	while (is_array($line = array_shift($lines))) {

		if (count($line) == 1) {
//			echo $line[0];
			continue;
		}

		$thisData = array();
		foreach ($line as $i => $token) {
			if (! $i) continue;
			array_push($thisData, preg_replace("/[^0-9]/", "", $token));
		}

		$data[$line[0]] = $thisData;


	}


	echo json_encode($data);

}

getBlsData($_GET['url']);

/*

	javascript:(window.location='http://localhost:8888/pi/platforms/data/bls.php?url='+window.location.href;)

*/

