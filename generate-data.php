<?php

require_once "util.php";

function random_property($min = 0, $max = 1, $props = array())
{
    $a = array();
    for ($i = 0; $i < rand($min, $max); $i++) {
        $p = random_from($props);
        if (in_array($p, $a)) {
            continue; // prevent duplicates
        }
        array_push($a, $p);
    }
    return $a;
}

function random_names($data) {
    $randomnames = array();
    $lastnamearrays = array($data["lastnames-0"], $data["lastnames-1"], $data["lastnames-2"], $data["lastnames-3"], $data["lastnames-4"]);
    for ($x = 0; $x < 2; $x++) {
        foreach ($lastnamearrays as $lastnames) {
            foreach ($lastnames as $lastname) {
                $name = [];
                $name['first'] = random_from($data["firstnames"]);
                $name['last'] = $lastname;
                array_push($randomnames, $name);
            }
        }
    }
    return $randomnames;
}

// currently generating:
// 10,000 random patients

$conditions = array("Milk", "Eggs", "Peanuts", "Tree nuts", "Soy", "Wheat", "Fish", "Shellfish", "Seeds", "Gluten", "Flour", "Pollen", "Mold", "Dust", "Latex", "Meat", "Bees", "Dog", "Cat", "Aquagenic urticaria");
$medications = array("Atorvastatin", "Cholestyramine", "Choline", "Fenofibrate", "Colestipol", "CRESTOR", "Fenofibrate", "Micronized fenofibric", "Gemfibrozil", "Lovastatin", "Niaci", "Pravastatin", "Simvastatin", "acetaZOLAMIDE", "acetoHEXAMIDE", "busPIRone", "buPROPion", "chlorproPAMIDE", "chlorproMAZINE", "clomiPHENE", "clomiPRAMINE", "cycloSERINE", "cycloSPORINE", "cycloSERINE", "diphenhydrAMINE", "dimenhyDRINATE", "DOPamine", "DOBUTamine", "DOXOrubicin", "DAUNOrubicin");
$names = json_from("names.json");
$built = array();

foreach (random_names($names) as $name) {
    $newpatient = [];
    $newpatient["first"] = $name["first"];
    $newpatient["last"] = $name["last"];
    $newpatient["id"] = rand(1000001, 9999999);
    $newpatient["dob"] = rand(31791600, 1072652400); // unix
    $newpatient["w"] = rand(95, 250); // pounds
    $newpatient["h"] = rand(50, 84); // inches
    $newpatient["notes"] = random_string(32);
    $newpatient["g"] = random_from();
    $newpatient["conditions"] = random_property(-3, 4, $conditions);
    $newpatient["meds"] = random_property(-2, 5, $medications);
    array_push($built, $newpatient);
}

// be mindful of PHP memory limit

json_to_file($built, "rawdata.json");

echo "Done!
Sort now:
<a href=\"sort.php?go=0\">Last name</a>
<a href=\"sort.php?go=1\">First name</a>
<a href=\"sort.php?go=2\">Patient ID</a>";
