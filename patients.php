<?php

echo "don't need";

//$arrField = [];
//$arrField['id'] = '0001';
//$arrField["first_name"] ='Demo Test';
//$fh = fopen("data_out.json", 'w')
//      or die("Error opening output file");
//fwrite($fh, json_encode($data));
//fclose($fh);

/*$patients = [];

$actualfile = file_get_contents("names.json");
$jsondata = json_decode($actualfile, true);

$lastnamearrays = array($jsondata["lastnames-0"], $jsondata["lastnames-1"], $jsondata["lastnames-2"], $jsondata["lastnames-3"], $jsondata["lastnames-4"]);

foreach ($lastnamearrays as $lastnames) {
    foreach ($lastnames as $lastname) {
        $patient = [];
        $patient['first'] = $jsondata["firstnames"][rand(0, 999)];
        $patient['last'] = $lastname;
        array_push($patients, $patient);
    }
}

$wr = fopen("pats.json", "w");
fwrite($wr, json_encode($patients));
fclose($wr);*/