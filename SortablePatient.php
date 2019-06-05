<?php

class SortablePatient
{
    public static $LAST_NAME = 0;
    public static $FIRST_NAME = 1;
    public static $ID = 2;

    public $data;

    function __construct()
    {
        $this->data = func_get_arg(0);
    }

    public function get_lastname()
    {
        return $this->data["last"];
    }

    public function get_firstname()
    {
        return $this->data["first"];
    }

    public function get_id()
    {
        return $this->data["id"];
    }

    public function get_dob()
    {
        return $this->data["dob"];
    }

    public function get_weight()
    {
        return $this->data["weight"];
    }

    public function get_height()
    {
        return $this->data["height"];
    }

    public function get_notes()
    {
        return $this->data["notes"];
    }

    public function get_conditions()
    {
        return $this->data["conditions"];
    }

    public function get_meds()
    {
        return $this->data["meds"];
    }

    public function get_nth($type = -1, $n = -1)
    {
        $s = "";
        if (isset($type)) {
            switch ($type) {
                case SortablePatient::$FIRST_NAME:
                    $s = $this->get_firstname();
                    break;
                case SortablePatient::$LAST_NAME:
                    $s = $this->get_lastname();
                    break;
                case SortablePatient::$ID:
                    $s = strval($this->get_id());
                    break;
            }
        }
        if ($s === "") {
            return $s;
        }
        if ($n != -1) {
            return substr($s, $n, 1);
        }
        return substr($s, 0, 1);
    }

    public function __toString()
    {
        return $this->get_firstname() . " " . $this->get_lastname() . " (" . $this->get_id() . ")";
    }
}