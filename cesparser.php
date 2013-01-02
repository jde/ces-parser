<?php
header('Content-type: application/json');

function getHeader($line) {

	$ts = explode("  ", $line);
	$x = 0;
	while ($ts[$x] == "") {
		$x++;
	}
	return $section = str_replace(":", "", str_replace(".", "", trim($ts[$x])));
}

function metafy($lines) {

	$section = "";
	$subsection = "";

	foreach ($lines as $i => $line) {

		if ($i < 10) continue;

		if (substr($line, 0, 1) != " ") {
			$section = getHeader($line);
			$subsection = "";
		}

		if (substr($line, 0, 2) != "  ") {
			$subsection = getHeader($line);
		}

		$lines[$i] = array(
			'section' => $section,
			'subsection' => $subsection,
			'text' => $line
		);

	}

	return $lines;

}

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

	}

	$lines = metafy($lines);

	foreach ($lines as $i => $line) {

		@$lines[$i]['tokens'] = tokenize($line['text']);
	}

	// Step 1 - get the titile
	$data['title'] = '';
	$line = array_shift($lines);
	while (count($line['tokens']) === 1) {
		$data['title'] .= $line[0];
		$line = array_shift($lines);
	}
	array_unshift($lines, $line); // put that last one back on ;)


	// Step 2 - combine the next three lines as the group headers
	$data['groups'] = array();
	$g1 = array_shift($lines);
	$g2 = array_shift($lines);
	$g3 = array_shift($lines);
	foreach ($g1['tokens'] as $i => $g) {
		$data['groups'][$i] = array(
			'text' => @$g1['tokens'][$i] . " " . @$g2['tokens'][$i] . " " . @$g3['tokens'][$i],
			'low' => $g1['tokens'][$i],
			'high' => $g3['tokens'][$i],
		);
	}

	// Step 3 - charge through and get the lines of data
	while (is_array($line = array_shift($lines))) {

		if (count($line['tokens']) == 1) {
//			echo $line[0];
			continue;
		}

		$thisData = array();
		foreach ($line['tokens'] as $i => $token) {
			if (! $i) continue;
			array_push($thisData, preg_replace("/[^0-9]/", "", $token));
		}

		$data[$line['tokens'][0]] = array('values' => $thisData, 'meta' => $line);


	}


	echo json_encode($data);

}

getBlsData($_GET['url']);

/*

	javascript:(window.location='http://localhost:8888/pi/platforms/data/bls.php?url='+window.location.href;)

*/

