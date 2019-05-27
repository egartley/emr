<?php

// Credit: https://stackoverflow.com/a/4356295
function random_string($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // store string length in variable as to not re-calc it everytime
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$conditions = array("Milk", "Eggs", "Peanuts", "Tree nuts", "Soy", "Wheat", "Fish", "Shellfish", "Seeds", "Gluten", "Flour", "Pollen", "Mold", "Dust", "Latex", "Meat", "Bees", "Dog", "Cat", "Aquagenic urticaria");

$newdata = array();
$randomnames = array();

$names = json_decode(file_get_contents("names.json"), true);
$lastnamearrays = array($names["lastnames-0"], $names["lastnames-1"], $names["lastnames-2"], $names["lastnames-3"], $names["lastnames-4"]);

// 100 random first names
// 500 random last names (possible duplicates)

for ($x = 0; $x < 10; $x++) {
    foreach ($lastnamearrays as $lastnames) {
        foreach ($lastnames as $lastname) {
            $name = [];
            $name['first'] = $names["firstnames"][rand(0, 999)];
            $name['last'] = $lastname;
            array_push($randomnames, $name);
        }
    }
}

foreach ($randomnames as $name) {
    $newpatient = [];
    $newpatient["first"] = $name["first"];
    $newpatient["last"] = $name["last"];
    $newpatient["id"] = rand(1000001, 9999999);
    $newpatient["dob"] = rand(31791600, 1072652400); // unix
    $newpatient["weight"] = rand(95, 250); // pounds
    $newpatient["height"] = rand(50, 84); // inches
    $newpatient["notes"] = random_string(32);

    $c = array();
    for ($i = 0; $i < rand(-3, 4); $i++) {
        array_push($c, $conditions[rand(0, count($conditions) - 1)]);
    }
    $newpatient["conditions"] = $c;

    $m = array();
    for ($i = 0; $i < rand(-2, 5); $i++) {
        array_push($m, rand(1, 20));
    }
    $newpatient["meds"] = $m;

    array_push($newdata, $newpatient);
}

$filehook = fopen("rawdata.json", "w");
fwrite($filehook, json_encode($newdata));
fclose($filehook);

echo "Done!\nSort now:\n<a href=\"sort.php?go=0\">Last name</a>\n<a href=\"sort.php?go=1\">First name</a>\n<a href=\"sort.php?go=2\">Patient ID</a>";
