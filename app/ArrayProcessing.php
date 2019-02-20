<?php
namespace ClinicsList;
  
class ArrayProcessing
{
  public function get_array($table_array) {
    $assoc_arr = [];
    $keys = [];
    foreach ($table_array as $index => $row) {
      if ($index == 0) {
        foreach ($row as $key) {
          array_push($keys, strtolower(str_replace(' ', '_', $key)));
        }
      } else {foreach ($row as $index => $value) {
          $assoc_row[$keys[$index]] = $value;
      }
        array_push($assoc_arr, $assoc_row);  
      }  
    }

    $arrays_stack = array('keys' => $keys, 'assoc' => $assoc_arr);

    return $arrays_stack;
  }

  public function get_keys($table_array)
  {
    $keys_list = 'Array keys is:<br>';
    foreach ($table_array[0] as $key) {
      $keys_list .= strtolower(str_replace(' ', '_', $key)) ."<br>";
    }

    return $keys_list;
  }
}  
