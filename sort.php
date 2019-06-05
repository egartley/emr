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

function go($type)
{
    $datafilecontents = file_get_contents("rawdata.json");
    $jsondata = json_decode($datafilecontents, true);

    $patients = array();
    foreach ($jsondata as $patient) {
        array_push($patients, new SortablePatient($patient));
    }

    $alphanumericized = array();
    $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
    $allnumbers = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
    if ($type == SortablePatient::$ID) {
        foreach ($allnumbers as $number) {
            foreach ($patients as $p) {
                if ($p->get_nth(SortablePatient::$ID) === $number) {
                    if ($p->get_id() === 1595944) {
                        echo "8124823649732456332325496234625<br><br>";
                    }
                    array_push($alphanumericized, $p);
                }
            }
        }
    } else {
        foreach ($alphabet as $letter) {
            foreach ($patients as $p) {
                if ($p->get_nth($type) === $letter) {
                    array_push($alphanumericized, $p);
                }
            }
        }
    }

    set_time_limit(300);
    sort_by_type($alphanumericized, $type);

    if ($type == SortablePatient::$ID) {
        echo "Sorted by ID, see <a href=\"/index/id/\" target=\"_blank\">/index/id/</a>";
        return;
    } else if ($type == SortablePatient::$FIRST_NAME) {
        echo "Sorted by first name, see <a href=\"/index/first/\" target=\"_blank\">/index/first/</a>";
        return;
    } else if ($type == SortablePatient::$LAST_NAME) {
        echo "Sorted by last name, see <a href=\"/index/last/\" target=\"_blank\">/index/last/</a>";
        return;
    }
}

if (isset($_GET["go"])) {
    go($_GET["go"]);
}

function sort_by_type($data, $type)
{
    if ($type == SortablePatient::$ID) {
        $nonzero = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
        $withzero = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        foreach ($nonzero as $number1) {
            foreach ($withzero as $number2) {
                foreach ($withzero as $number3) {
                    $patswithnn = array();
                    $s = strval($number1) . strval($number2) . strval($number3);
                    foreach ($data as $pat1) {
                        if ($s === strval($pat1->get_nth(SortablePatient::$ID)) . strval($pat1->get_nth(SortablePatient::$ID, 1)) . strval($pat1->get_nth(SortablePatient::$ID, 2))) {
                            array_push($patswithnn, $pat1);
                        }
                    }
                    store_by_type($patswithnn, $s, SortablePatient::$ID);
                }
            }
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
        store_by_type($sorted, $currentletter, $type);
    }
}

function store_by_type($sorted, $letter, $type)
{
    $d = "last";
    if ($type == SortablePatient::$FIRST_NAME) {
        $d = "first";
    } else if ($type == SortablePatient::$ID) {
        $d = "id";
    }
    $file = fopen("index/" . $d . "/" . strtolower($letter) . ".json", "w");
    fwrite($file, json_encode($sorted));
    fclose($file);
}

?>
