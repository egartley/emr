<h2>sortme</h2>

<script type="application/javascript" src="jquery.js"></script>

<button onclick="go(0)">Sort By Last Name</button><br><br>
<button onclick="go(1)">Sort By First Name</button><br><br>

<?php

require_once 'SortablePatient.php';

function store_letter($sorted, $letter, $type) {
    $d = "lastname";
    if ($type == SortablePatient::$FIRST_NAME) {
        $d = "firstname";
    }
	$filehook = fopen("index/" . $d . "/" . strtolower($letter) . ".json", "w");
	fwrite($filehook, json_encode($sorted));
	fclose($filehook);
}

function initial_sort($data, $store, $type) {
    $sortedbyletter = array();
    $absindex = 0;
    for ($letterindex = 0; $letterindex < 26; $letterindex++) {
        $sorted = array();
        if ($absindex >= count($data)) {
        	continue;
        }
        $currentletter = SortablePatient::get_letter($data[$absindex], $type);
        while ($currentletter === SortablePatient::get_letter($data[$absindex], $type)) {
            array_push($sorted, $data[$absindex]);
            $absindex++;
            if ($absindex >= count($data)) {
                break;
            }
        }
        array_push($sortedbyletter, $sorted);
        if ($store && isset($type)) {
            store_letter($sorted, $currentletter, $type);
        }
    }
    return $sortedbyletter;
}

function go($type) {
	$datafilecontents = file_get_contents("sortednew.json");
	$jsondata = json_decode($datafilecontents, true);
	$pats = array();
	foreach ($jsondata as $patient) {
    	array_push($pats, new SortablePatient($patient["last"], $patient["first"], $patient["id"]));
	}
	$sport = array();
	$order = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
	foreach ($order as $letter) {
	    foreach ($pats as $p) {
	        if (SortablePatient::get_letter($p, $type) === $letter) {
	            array_push($sport, $p);
            }
        }
    }

	$maybe = initial_sort($sport, true, $type);

	foreach ($maybe as $out) {
    	print_r($out);
    	echo "<br><br>";
	}
}

if (isset($_GET["go"])) {
    // date_default_timezone_set('America/New_York');
    // $n = microtime(true);
    // echo "Start: " . $n . "<br>";
    go($_GET["go"]);
    // $el = (round(microtime(true) * 1000) - round($n * 1000)) / 1000;
    // echo "Elasped: " . $el . " seconds";
}

?>

<script type="application/javascript">
    function go(n) {
        window.location = "sortme.php?go=" + n
    }
</script>
