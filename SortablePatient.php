<?php

class SortablePatient
{
    public static $LAST_NAME = 0;
    public static $FIRST_NAME = 1;
    public static $ID = 2;

    public $lastname;
    public $firstname;
    public $id;
    public $weight;
    public $height;
    public $notes;
    public $dob;
    public $conditions;
    public $meds;

    function __construct()
    {
        $this->lastname = func_get_arg(0);
        $this->firstname = func_get_arg(1);
        $this->id = func_get_arg(2);
        $this->weight = func_get_arg(3);
        $this->height = func_get_arg(4);
        $this->notes = func_get_arg(5);
        $this->dob = func_get_arg(6);
        $this->conditions = func_get_arg(7);
        $this->meds = func_get_arg(8);
    }

    public static function get_letter(SortablePatient $patient, $type = -1, $n = -1) {
        $str = "";
        if (isset($type)) {
            switch ($type) {
                case SortablePatient::$FIRST_NAME:
                    $str = $patient->firstname;
                    break;
                case SortablePatient::$LAST_NAME:
                    $str = $patient->lastname;
                    break;
                case SortablePatient::$ID:
                    $str = strval($patient->id);
                    break;
            }
        }
        if ($n != -1) {
            return substr($str, $n, $n + 1);
        }
        return substr($str, 0, 1);
    }

    public function __toString()
    {
        return $this->firstname . " " . $this->lastname . " (" . $this->id . ")";
    }
}