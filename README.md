# FMTClinics

## Install 

  1) For use this APP, you must have a [PHP(7.~ v)](http://php.net/downloads.php), HTTP server and a [composer](https://getcomposer.org/)
  2) Put files in HTTP Server root directory.
  4) Update composer `composer update`
  3) Run in terminal `php index.php`
  
  
## Use

  To create pages, select the appropriate table fields.

  ```php 
    $filest->create($assoc_arr['assoc'], __DIR__, ['country', 'city'];    
  ```
  where `country` and `cuty` - field names

  or for selection by two fields instead of a string, enter an array of two strings

  ```php 
    $filest->create($assoc_arr['assoc'], __DIR__, ['country', ['city' , 'country']];    
  ```

