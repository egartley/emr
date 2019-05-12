<?php

// Credit: https://stackoverflow.com/a/4356295
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$conds = array("Milk", "Eggs", "Peanuts", "Tree nuts", "Soy", "Wheat", "Fish", "Shellfish", "Seeds", "Gluten", "Flour", "Pollen", "Mold", "Dust", "Latex", "Meat", "Bees", "Dog", "Cat", "Aquagenic urticaria");

$newdata = array();
$patients = array();

$actualfile = file_get_contents("names.json");
$jsondata = json_decode($actualfile, true);
$lastnamearrays = array($jsondata["lastnames-0"], $jsondata["lastnames-1"], $jsondata["lastnames-2"], $jsondata["lastnames-3"], $jsondata["lastnames-4"]);

for ($x = 0; $x < 10; $x++) {
    foreach ($lastnamearrays as $lastnames) {
        foreach ($lastnames as $lastname) {
            $patient = [];
            $patient['first'] = $jsondata["firstnames"][rand(0, 999)];
            $patient['last'] = $lastname;
            array_push($patients, $patient);
        }
    }
}

foreach ($patients as $patient) {
    $newpatient = [];
    $newpatient["first"] = $patient["first"];
    $newpatient["last"] = $patient["last"];
    $newpatient["id"] = rand(1000001, 9999999);
    $newpatient["dob"] = strtotime(rand(1971, 2004) . "W01");
    $newpatient["weight"] = rand(45, 250);
    $newpatient["height"] = rand(45, 84);
    $newpatient["notes"] = generateRandomString(300);

    $c = array();
    for ($i = 0; $i < rand(1, 8); $i++) {
        array_push($c, $conds[rand(0, count($conds) - 1)]);
    }
    $newpatient["conditions"] = $c;

    $c = array();
    for ($i = 0; $i < rand(0, 6); $i++) {
        array_push($c, rand(1, 20));
    }
    $newpatient["meds"] = $c;

    array_push($newdata, $newpatient);
}

$filehook = fopen("rawdata_new.json", "w");
fwrite($filehook, json_encode($newdata));
fclose($filehook);

echo "Done\n<a href=\"sort.php\">Sort now</a>";
