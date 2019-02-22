<?php
require_once 'vendor/autoload.php';

use ClinicsList\ArrayProcessing as ArrayProcessing;
use ClinicsList\FileStructure as FileStructure;

$arrays = new ArrayProcessing;
$filest = new FileStructure;

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');
$client->setApplicationName('GetGoogleSheets');
$client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
//$client->setAccessType('offline');
//$client->setPrompt('select_account consent');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
  } 
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
} else {
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/FMTClinics/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
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
  ['city', 'country'], 
  ['city','state']]
);


