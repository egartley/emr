<?php

require_once 'util.php';
require_once 'SortablePatient.php';

if (isset($_GET["go"])) {
    go($_GET["go"]);
}

function go($type)
{
    // build patient objects array from json
    $patients = array();
    foreach (json_from("rawdata.json") as $patient) {
        array_push($patients, new SortablePatient($patient));
    }

    set_time_limit(60 * 3);
    sort_patients($patients, $type);

    if ($_GET["all"] === "yes") {
        if ($type != 2) {
            header("Location: /sort.php?go=" . ($type + 1) . "&all=yes");
        } else {
            echo "Sorted by all types, see <a href=\"/index/\" target=\"_blank\">/index/</a>";
            return;
        }
    }

    if ($type == SortablePatient::$ID) {
        echo "Sorted by ID, see <a href=\"/index/id/\" target=\"_blank\">/index/id/</a>";
    } else if ($type == SortablePatient::$FIRST_NAME) {
        echo "Sorted by first name, see <a href=\"/index/first/\" target=\"_blank\">/index/first/</a>";
    } else if ($type == SortablePatient::$LAST_NAME) {
        echo "Sorted by last name, see <a href=\"/index/last/\" target=\"_blank\">/index/last/</a>";
    }
}

function sort_patients($patients, $type)
{
    if ($type == SortablePatient::$ID) {
        $nonzero = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
        $withzero = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        foreach ($nonzero as $number1) {
            foreach ($withzero as $number2) {
                foreach ($withzero as $number3) {
                    $matches = array();
                    $currentfirstthree = strval($number1 . $number2 . $number3);
                    foreach ($patients as $p) {
                        $pfirstthree = substr(strval($p->get_id()), 0, 3);
                        if ($currentfirstthree === $pfirstthree) {
                            array_push($matches, $p);
                        }
                    }
                    store_by_type($matches, $currentfirstthree, SortablePatient::$ID);
                }
            }
        }
        return;
    }

    $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
    foreach ($alphabet as $currentletter) {
        $patientswithcurrentletter = array();
        foreach ($patients as $p) {
            if ($p->get_nth($type) === $currentletter) {
                array_push($patientswithcurrentletter, $p);
            }
        }
        store_by_type($patientswithcurrentletter, $currentletter, $type);
    }
}

function store_by_type($sorted, $letter, $type)
{
    $subdir = "last";
    if ($type == SortablePatient::$FIRST_NAME) {
        $subdir = "first";
    } else if ($type == SortablePatient::$ID) {
        $subdir = "id";
    }
    json_to_file($sorted, "index/" . $subdir . "/" . strtolower($letter) . ".json");
}

?>
