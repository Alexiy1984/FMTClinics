<?php
require_once 'vendor/autoload.php';

use ClinicsList\ArrayProcessing as ArrayProcessing;
use ClinicsList\FileStructure as FileStructure;

$arrays = new ArrayProcessing;
$filest = new FileStructure;

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
    array_push($values_array, $row);
  }
}

$countries = [];
$assoc_arr = [];
$keys = [];

$assoc_arr = $arrays->get_array($values_array);

echo $arrays->get_keys($values_array);

$filest->create($assoc_arr['assoc'], __DIR__, [
  'country', 
  'city', 
  'state', 
  ['country', 'city'], 
  ['city','state']]
);


