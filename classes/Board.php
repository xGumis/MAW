<?php
require('Field.php');
class Board
{
    var $fields = array();
    function __construct(){
        for($i=0;$i<24;$i++)
            $this->fields[] = new Field($i);
        for($i=0;$i<8;$i++){
            $this->fields[($i*3)]->add_line1($this->fields[($i*3)+1]);
            $this->fields[($i*3)]->add_line1($this->fields[($i*3)+2]);
            $this->fields[($i*3)+1]->add_line1($this->fields[($i*3)+2]);
        }
        $this->fields[0]->add_line2($this->fields[9]);
        $this->fields[0]->add_line2($this->fields[21]);
        $this->fields[3]->add_line2($this->fields[10]);
        $this->fields[3]->add_line2($this->fields[18]);
        $this->fields[1]->add_line2($this->fields[4]);
        $this->fields[1]->add_line2($this->fields[7]);
        $this->fields[16]->add_line2($this->fields[19]);
        $this->fields[16]->add_line2($this->fields[22]);
        $this->fields[8]->add_line2($this->fields[12]);
        $this->fields[8]->add_line2($this->fields[17]);
        $this->fields[5]->add_line2($this->fields[13]);
        $this->fields[5]->add_line2($this->fields[20]);
        $this->fields[2]->add_line2($this->fields[14]);
        $this->fields[2]->add_line2($this->fields[23]);
        $this->fields[6]->add_line2($this->fields[11]);
        $this->fields[6]->add_line2($this->fields[15]);
        $this->fields[9]->add_line2($this->fields[21]);
        $this->fields[10]->add_line2($this->fields[18]);
        $this->fields[11]->add_line2($this->fields[15]);
        $this->fields[4]->add_line2($this->fields[7]);
        $this->fields[19]->add_line2($this->fields[22]);
        $this->fields[12]->add_line2($this->fields[17]);
        $this->fields[13]->add_line2($this->fields[20]);
        $this->fields[14]->add_line2($this->fields[23]);
    }
    function check($id)
    {
        $matched = 0;
        $field = $this->fields[$id];
        $value = $field->get();
        if($value==$field->line1[0]->get()&&$value==$field->line1[1]->get()) $matched++;
        if($value==$field->line2[0]->get()&&$value==$field->line2[1]->get()) $matched++;
        return $matched;
    }
    function move($from,$to,$spec){
        $from = $this->fields[$from];
        if($spec||($from->line1[0]->id==$to||$from->line1[1]->id==$to)||($from->line2[0]->id==$to||$from->line2[1]->id==$to)){
            $value = $from->get();
            $to = $this->fields[$to];
            if($to->change($value))return $from->change(0);
        }
        return false;
    }
    function place($value,$field){
        $field = $this->fields[$field];
        return $field->change($value);
    }
    function remove($field){
        $field = $this->fields[$field];
        $value = $field->get();
        if($value!=0){
            if(
            !($field->line1[0]->get()==$value && $field->line1[1]->get()==$value)||
            !($field->line2[0]->get()==$value && $field->line2[1]->get()==$value)
            ) return $field->change(0);
        }
        return false;
    }
}