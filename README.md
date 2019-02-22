# FMTClinics

## Install 

  1) For use this APP, you must have a [PHP(7.~ v)](http://php.net/downloads.php), HTTP server and a [composer](https://getcomposer.org/)
  2) Put files in HTTP Server root directory ro run server with this directory as document root.
  3) Run `composer install` to install all app dependencies
  4) Go to [Google developers console](https://console.developers.google.com/) and create project for [Google Sheets API](https://developers.google.com/sheets/api/)
  5) Create credentials for your web app and add cllaback page (oauth2callback.php) to erdirect URIs:
    your project -> credentials -> create credentials ->  OAuth client ID -> Application type : Web application ->
    Authorized redirect URIs : [your host]/[your main dir name]/oauth2callback.php
  6) Save credentials and dowload json
  7) Copy all json data to client_secrets.json
  8) Open your index page in browser
  9) Confirm access for the application 
  
## Use

  For get google sheet header(keys) use ArrayProcessing class method - `get_keys()`

  ```php
    $array = new ArrayProcessing;
    echo $array->get_keys($values_array);

  ```

  To create pages, select the appropriate table fields.

  ```php 
    $filest->create($assoc_arr['assoc'], __DIR__, ['country', 'city'];    
  ```
  where `country` and `cuty` - field names

  or for selection by two fields instead of a string, enter an array of two strings

  ```php 
    $filest->create($assoc_arr['assoc'], __DIR__, ['country', ['city' , 'country']];    
  ```

