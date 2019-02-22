<?php
namespace ClinicsList;
  
class FileStructure
{
  public function create($assoc_arr ,$cur_dir, $keyval)
  {
    $unique = [];

    foreach ($keyval as $ch_dir) {
      $unique_val = [];

      $this->main_dir_create($cur_dir, $ch_dir);

      if (gettype($ch_dir) == 'array') {
        if (count($ch_dir) == 2) {
          $key_m = "$ch_dir[0]_$ch_dir[1]";
          foreach ($assoc_arr as $row) {
            $match = 0;
            $mvalue = '';
            foreach ($row as $key => $value) {
              if ($key == $ch_dir[0]) {
                if ($value != '') {
                  $match = $match + 1;
                  $value = str_replace('->', '', $value); 
                  //$mvalue .= "$value->";
                  $mvalue = sprintf('%1$s%2$s', "$value->", $mvalue);
                }
              };
              if ($key == $ch_dir[1]) {
                if ($value != '') {
                  $match = $match + 1; 
                  $value = str_replace('->', ' ', $value); 
                  $mvalue = sprintf('%1$s%2$s', $mvalue, "$value->");
                }  
              }; 
            }
            if ($match == 2) array_push($unique_val, $mvalue);
          }
        }
      } else  {
        $key_m = $ch_dir;
        foreach ($assoc_arr as $row) {
          foreach ($row as $key => $value) {
            if ($key == $ch_dir) {
              $value = str_replace('->', ' ', $value); 
              if ($value != '') array_push($unique_val, $value);
            }
          }
        }    
      }  
      
      $unique[$key_m] = array_unique($unique_val);
    }

    // var_dump($unique['city_state']);
    // echo "<br>";

    foreach ($unique as $key => $value_array) {
      foreach ($value_array as $value) {
        $this->write_file($cur_dir ,$key, $value, $assoc_arr); 
      }    
    }
  }

  private function main_dir_create ($cur_dir, $ch_dir) {
    if (gettype($ch_dir) == 'array') {
      if (count($ch_dir) == 2) $f_dir = "$ch_dir[0]_$ch_dir[1]";
    } else $f_dir = $ch_dir; 
    if (!file_exists("$cur_dir/table_data/$f_dir")) {
      mkdir("$cur_dir/table_data/$f_dir", 0777, true);
      chmod("$cur_dir/table_data/$f_dir", 0777);
    }
  }

  private function write_file($dir, $key, $value, $data) 
  {
    if (stripos($value, '->') > 0) {
      $pos = strripos($value, '->');
      $name = strtolower(str_replace([' ', '->'], '_', substr_replace($value, '', $pos)));
      $filename = "$dir/table_data/$key/" . $name .".php";
      $value_a = explode('->', $value);
      $key_a = explode('_', $key);
      $two_n = true;
    }
    else {
      $filename = "$dir/table_data/$key/" . strtolower(str_replace(' ', '_', $value)) .".php";
      $two_n = false;
    }
    $fh = fopen($filename, "w");
    if (!is_resource($fh)) {
        return false;
    }
    $string .= "<h1>". ucfirst($key) ." : " .ucfirst($value) ."</h1>";
    if (!$two_n) {
      foreach ($data as $row) {
        if (strtolower(str_replace(' ', '_', $row[$key])) == strtolower(str_replace(' ', '_', $value))) {
          $string .= "<div>";
          foreach ($row as $key => $value) {
            $string .= "<p>$key = $value</p>";
          }
          $string .= "</div>";
        }
      }
    } else {
      foreach ($data as $row) {
        if ($row[$key_a[0]] == $value_a[0] && $row[$key_a[1]] == $value_a[1]) {
          $string .= "<div>";
          foreach ($row as $key => $value) {
            $string .= "<p>$key = $value</p>";
          }
          $string .= "</div>";
        }
        // echo $key_a[0] .":" .$row[$key_a[0]] ."> $value_a[0]<br>";
        // echo $key_a[1] .":" .$row[$key_a[1]] ."> $value_a[1]<br>";
        //echo "$key : $value<br>";
      }
      // echo "$key_a[0] : $key_a[1];";
    }
    fwrite($fh, sprintf("%s\n", $string));
    fclose($fh);
    chmod($filename, 0777);
    
    return true;
  }
}
