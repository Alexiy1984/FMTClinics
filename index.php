<?php
require_once 'vendor/autoload.php';

// if (php_sapi_name() != 'cli') {
//   throw new Exception('This application must be run on the command line.');
// }

function getClient()
{
  $client = new Google_Client();
  $client->setApplicationName('Google Sheets API PHP Quickstart');
  $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
  $client->setAuthConfig('credentials.json');
  $client->setAccessType('offline');
  $client->setPrompt('select_account consent');

  $tokenPath = 'token.json';
  if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
  }

  if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    } else {
      $authUrl = $client->createAuthUrl();
      printf("Open the following link in your browser:\n%s\n", $authUrl);
      print 'Enter verification code: ';
      $authCode = trim(fgets(STDIN));

      $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
      $client->setAccessToken($accessToken);
      
      if (array_key_exists('error', $accessToken)) {
        throw new Exception(join(', ', $accessToken));
      }
    }
    if (!file_exists(dirname($tokenPath))) {
      mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
  }
    return $client;
}

$client = getClient();
$service = new Google_Service_Sheets($client);

$spreadsheetId = '1KrJppgU8OCEb4rC2glZTjVd7kJ0FXVEeGQ9YszE5B6M';
$range = 'Stem Cell Clinic';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
$values_array = [];

if (empty($values)) {
  print "No data found.\n";
} else {
  foreach ($values as $row) {
    //printf("%s, %s\n", $row[0], $row[4]);
    //echo $row[0].' '.$row[1].' '.$row[4];
    array_push($values_array, $row);
  }
}

$countries = [];
$assoc_arr = [];
$keys = [];


foreach ($values_array as $index => $row) {
  if ($index == 0) {
    foreach ($row as $key) {
      array_push($keys,  strtolower(str_replace(' ', '_', $key)));
    }
  } else  {
    array_push($countries, strtolower(str_replace(' ', '_', $row[6])));
    foreach ($row as $index => $value) {
      $assoc_row[$keys[$index]] = $value;
    }
    array_push($assoc_arr, $assoc_row);  
  }  
}

$countries = array_unique($countries);
//var_dump($countries);
foreach ($assoc_arr as $value) {
  //echo "<p>".$value['country']."</p>";
}
//var_dump($assoc_arr[0]);
//var_dump($keys);


foreach ($countries as $country) {
  writeConfig($country, $assoc_arr); 
}

function writeConfig($country, $data) 
{
  $dir = __DIR__;
  $filename = "$dir/countries/$country.php";
  $fh = fopen($filename, "w");
  if (!is_resource($fh)) {
      return false;
  }
  $string .= "<h1>$country</h1>";
  foreach ($data as $row) {
    if (strtolower(str_replace(' ', '_', $row['country'])) == $country) {
      $string .= "<div>";
      foreach ($row as $key => $value) {
        $string .= "<p>$key = $value</p>";
      }
      $string .= "</div>";
    }
  }
  fwrite($fh, sprintf("%s\n", $string));
  fclose($fh);

  return true;
}
?>

<ul>
  <?php foreach ($countries as $country) :?>
    <li>
      <a href='<?= "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."countries/".$country.".php" ?>'>
        <?= $country ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
