<style>
    :root {
        font-family: sans-serif
    }
</style>

<h1>Manage Patient Data</h1>
<p>Click one of the buttons below</p>

<p>
    <button onclick="go(0)">Sort By Last Name</button>
    <br><br>
    <button onclick="go(1)">Sort By First Name</button>
    <br><br>
    <button onclick="go(2)">Sort By ID</button>
</p>

<script type="application/javascript">
    function go(n) {
        window.location = "sort.php?go=" + n
    }
</script>

<?php

require_once 'SortablePatient.php';

if (isset($_GET["go"])) {
    go($_GET["go"]);
}

function store_letter($sorted, $letter, $type)
{
    $d = "lastname";
    if ($type == SortablePatient::$FIRST_NAME) {
        $d = "firstname";
    } else if ($type == SortablePatient::$ID) {
        $d = "id";
    }
    $file = fopen("index/" . $d . "/" . strtolower($letter) . ".json", "w");
    fwrite($file, json_encode($sorted));
    fclose($file);
}

function initial_sort($data, $store, $type)
{
    if ($type == SortablePatient::$ID) {
        $numbers = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
        foreach ($numbers as $number) {
            $patswith = array();
            foreach ($data as $pat) {
                if ($number === SortablePatient::get_letter($pat, $type)) {
                    array_push($patswith, $pat);
                }
            }
            if ($store) {
                store_letter($patswith, $number, $type);
            }
        }
        return null;
    }

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

function go($type)
{
    $datafilecontents = file_get_contents("rawdata.json");
    $jsondata = json_decode($datafilecontents, true);

    $patients = array();
    foreach ($jsondata as $patient) {
        array_push($patients, new SortablePatient($patient["last"], $patient["first"], $patient["id"], $patient["weight"], $patient["height"], $patient["notes"], $patient["dob"], $patient["conditions"], $patient["meds"]));
    }

    $semisorted = array();
    $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
    // all the A's, then all the B's, all the C's, etc.
    foreach ($alphabet as $letter) {
        foreach ($patients as $p) {
            $tt = $type;
            if ($type == SortablePatient::$ID) {
                $tt = SortablePatient::$LAST_NAME;
            }
            if (SortablePatient::get_letter($p, $tt) === $letter) {
                array_push($semisorted, $p);
            }
        }
    }

    $maybe = initial_sort($semisorted, true, $type);

    if ($maybe == null || $type == SortablePatient::$ID) {
        echo "Sorted by ID, see <a href=\"/index/id/\" target=\"_blank\">/index/id/</a>";
        return;
    }
}

?>
