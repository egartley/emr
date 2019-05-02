<?php

$contents = file_get_contents("data.json");
$patients = json_decode($contents, true)["patients"];
$newdata = array();

foreach ($patients as $patient) {
	$newpatient = [];
	$newpatient["first"] = $patient["first"];
	$newpatient["last"] = $patient["last"];
	$newpatient["id"] = rand(1000001, 9999999);
	array_push($newdata, $newpatient);
}

$filehook = fopen("sortednew.json", "w");
fwrite($filehook, json_encode($newdata));
fclose($filehook);
