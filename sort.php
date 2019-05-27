<style>
    :root {
        font-family: sans-serif
    }
</style>
<script type="application/javascript">
    function go(n) {
        window.location = "sort.php?go=" + n
    }
</script>
<h1>Manage Patient Data</h1>
<p>Click one of the buttons below</p>
<p>
    <button onclick="go(0)">Sort By Last Name</button>
    <br><br>
    <button onclick="go(1)">Sort By First Name</button>
    <br><br>
    <button onclick="go(2)">Sort By ID</button>
</p>

<?php

require_once 'SortablePatient.php';

function store_by_type($sorted, $letter, $type)
{
    $d = "lastname";
    if ($type == SortablePatient::$FIRST_NAME) {
        $d = "firstname";
    } else if ($type == SortablePatient::$ID) {
        $d = "id";
    }
    $file = fopen("index/" . $d . "/" . strtolower($letter) . ".json", "w");
    echo strlen(json_encode($sorted));
    echo "<br><br>";
    fwrite($file, json_encode($sorted));
    fclose($file);
}

function sort_by_type($data, $store, $type)
{
    if ($type == SortablePatient::$ID) {
        $numbers = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
        foreach ($numbers as $number) {
            $patientswith = array();
            foreach ($data as $pat) {
                if ($number === $pat->get_nth(SortablePatient::$ID)) {
                    // echo $pat->get_nth(SortablePatient::$ID);
                    array_push($patientswith, $pat);
                }
            }
            if ($store) {
                store_by_type($patientswith, $number, SortablePatient::$ID);
            }
            // echo "<br><br>";
        }
        return;
    }

    $sortedbyletter = array();
    $absindex = 0;
    for ($letterindex = 0; $letterindex < 26; $letterindex++) {
        $sorted = array();
        if ($absindex >= count($data)) {
            continue;
        }
        $currentletter = $data[$absindex]->get_nth($type);
        while ($currentletter === $data[$absindex]->get_nth($type)) {
            array_push($sorted, $data[$absindex]);
            $absindex++;
            if ($absindex >= count($data)) {
                break;
            }
        }
        array_push($sortedbyletter, $sorted);
        if ($store && isset($type)) {
            store_by_type($sorted, $currentletter, $type);
        }
    }
}

function go($type)
{
    $datafilecontents = file_get_contents("rawdata.json");
    $jsondata = json_decode($datafilecontents, true);

    $patients = array();
    foreach ($jsondata as $patient) {
        array_push($patients, new SortablePatient($patient));
    }

    $sortedalphabetically = array();
    $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
    foreach ($alphabet as $letter) {
        // all the A's, then all the B's, all the C's, etc.
        foreach ($patients as $p) {
            $tt = $type;
            if ($type == SortablePatient::$ID) {
                $tt = SortablePatient::$LAST_NAME;
            }
            if ($p->get_nth($tt) === $letter) {
                array_push($sortedalphabetically, $p);
            }
        }
    }

    sort_by_type($sortedalphabetically, true, $type);

    if ($type == SortablePatient::$ID) {
        echo "Sorted by ID, see <a href=\"/index/id/\" target=\"_blank\">/index/id/</a>";
        return;
    } else if ($type == SortablePatient::$FIRST_NAME) {
        echo "Sorted by first name, see <a href=\"/index/firstname/\" target=\"_blank\">/index/firstname/</a>";
        return;
    } else if ($type == SortablePatient::$LAST_NAME) {
        echo "Sorted by last name, see <a href=\"/index/lastname/\" target=\"_blank\">/index/lastname/</a>";
        return;
    }
}

if (isset($_GET["go"])) {
    go($_GET["go"]);
}

?>
