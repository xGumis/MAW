<?php
class Field
{
    public $line1 = array(); //1st indices to check
    public $line2 = array(); //2nd indices to check
    var $value; //0:empty, 1:player1, 2:player2
    function __construct(){
        $this->value = 0;
    }
    function change($value){
        if($value==0&&$this->value!=0){$this->value = $value;return true;}
        elseif($this->value==0){$this->value = $value;return true;}
        return false;
    }
    function get(){
        return $this->value;
    }
    function add_line1($field){
        array_push($this->line1, $field);
        array_push($field->line1, $this);
    }
    function add_line2($field){
        array_push($this->line2, $field);
        array_push($field->line2, $this);
    }
}