<?php

/**
 * DYNAMIC NAPBOTS ALLOCATION
 * (beta version, please double-check if its working properly)
 */


/**
 * Configurations variables here
 */


// Account details
$email = '';
$password = '';
$userId = ''; // How to find userId: https://imgur.com/a/fW4I8Be

// Weather dependent compositions
//  - Total of allocations should be equal to 1
//  - Leverage should be between 0.00 and 1.50
//  - How to find bot IDS: https://imgur.com/a/ayit9pR
$compositions = [
	'mild_bear' => [
		'compo' => [
			'STRAT_BTC_USD_FUNDING_8H_1' => 0.15,
			'STRAT_ETH_USD_FUNDING_8H_1' => 0.15,
			'STRAT_BTC_ETH_USD_H_1' => 0.70,
		],
		'leverage' => 1.0,
		'botOnly' => true
	],
	'mild_bull' => [
		'compo' => [
			'STRAT_BTC_USD_FUNDING_8H_1' => 0.25,
			'STRAT_ETH_USD_FUNDING_8H_1' => 0.25,
			'STRAT_BTC_ETH_USD_H_1' => 0.50,
		],
		'leverage' => 1.5,
		'botOnly' => true
	],
	'extreme' => [
		'compo' => [
			'STRAT_ETH_USD_H_3_V2' => 0.4,
			'STRAT_BTC_USD_H_3_V2' => 0.4,
			'STRAT_BTC_ETH_USD_H_1' => 0.2,
		],
		'leverage' => 1.0,
		'botOnly' => true
	]
];

/**
 * Script (do not touch here)
 */

$debug = false;
$verbose = false;
$dry_run = false;
$exchange_ignore_list = [];

if (file_exists('config.php')){
	include 'config.php';
}


function get_market(){
	// Get crypto weather
	$weatherApi = file_get_contents('https://middle.napbots.com/v1/crypto-weather');
	if($weatherApi) {
		$weather = json_decode($weatherApi,true)['data']['weather']['weather'];
	}
	return $weather;
}

function assign_composition($weather){
	$compositionToSet = null;
	global $compositions;
	// Find composition to set
	if($debug) echo "enter assign_composition [$weather]\n";
	if($weather === 'Extreme markets') {
		$compositionToSet = $compositions['extreme'];
	} elseif($weather === 'Mild bull markets'){
		$compositionToSet = $compositions['mild_bull'];
	} elseif($weather === 'Mild bear or range markets') {
		$compositionToSet = $compositions['mild_bear'];
	} else {
		throw new \Exception('Invalid crypto-weather: ' . $weather);
	}
	if($debug) echo "OUT assign_composition [$compositionToSet]\n";
	if($debug) var_dump($compositionToSet);
	return $compositionToSet;
}

function handle_args(){
	global $argv;
	global $verbose;
	global $forced_market;
	global $dry_run;
	global $debug;
	if(count($argv) > 1){
		if ($debug) { var_dump($argv); }
		if (in_array("force", $argv)) {
			if (in_array("extreme", $argv)) {
				$forced_market = "extreme";
			}
			elseif (in_array("mild_bull", $argv)) {
				$forced_market = "mild_bull";
			}
			elseif (in_array("mild_bear", $argv)) {
				$forced_market = "mild_bear";
			}
			echo "forced market to [$forced_market]\n";
		}
		if (in_array("dry", $argv)) { $dry_run = true; }
		if (in_array("verbose", $argv)) { $verbose = true; }
		if (in_array("debug", $argv)) { $verbose = true; $debug = true; }
	}
}

function check_compositions($compositions)
{
	global $verbose;
	global $debug;
	foreach($compositions as $weather => $composition) {
		if ($debug) echo("[$weather]\n");
		$sum = 0.0;
		foreach($composition['compo'] as $val){
			if (true === $debug) echo("add $val\n");
			$sum = floatval($val) + floatval($sum);
		}
		if ($debug) printf("sum: %.1f\n", $sum);
		if ($sum - 1.0 != 0){
			throw new \Exception("sum of you allocations for [$weather] is [$sum] it should be [1.0], check your numbers.");
		}
	}
	if($verbose) echo "composition sum is OK (1)\n";
}


$forced_market = "";
handle_args();

check_compositions($compositions);
if ("" === $forced_market) {
	$weather = get_market();
	$compositionToSet = assign_composition($weather);
} else {
	$weather = "FORCED";
	$compositionToSet = $compositions[$forced_market];
}

if ($verbose){
	echo "weather          [$weather]\n";
	echo "forced           [$forced_market]\n";
	echo "simulation mode  [$dry_run]\n";
	echo "compositionToSet [$compositionToSet]\n";
	if($debug) var_dump($compositionToSet);
}

if('array' != gettype($compositionToSet)) 
	throw new \Exception("error [$compositionToSet] is not an array, script is broken somewhere CANCELING EVERYTHING.\nAccount left untouched.\n\n");

// Log
echo "Crypto-weather is: " . $weather . "\n";

echo "authentication to napbots....\n";

// Login to app (get auth token)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://middle.napbots.com/v1/user/login' );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email, 'password' => $password])); 
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']); 
$response = curl_exec ($ch);
curl_close($ch);

$authToken = json_decode($response,true)['data']['accessToken'];
if(!$authToken) {
	throw new \Exception('Unable to get auth token.');
}

// Get current allocation for all exchanges
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://middle.napbots.com/v1/account/for-user/' . $userId);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'token: ' . $authToken]); 
$response = curl_exec ($ch);
curl_close($ch);

$data = json_decode($response,true)['data'];

echo "auth napbot OK\n";
// print var_dump($data);

// Rebuild exchanges array
$exchanges = [];
$exchanges_names = [];
foreach($data as $exchange) {
	if (in_array($exchange['exchange'], $exchange_ignore_list)){
		echo "as requested, ignoring [".$exchange['exchange']."]\n";
	       	continue;
	}

	if(empty($exchange['accountId']))
	{
		throw new \Exception('no exchange found');
	} else if (empty($exchange['compo'])) {
		var_dump($exchange);
		throw new \Exception("Invalid exchange data for [$exchange]");
	}

	$exchanges[$exchange['accountId']] = $exchange['compo'];
	$exchanges_names[$exchange['accountId']] = $exchange['exchange'];
}

// For each exchange, change allocation if different from crypto weather one
foreach($exchanges as $exchangeId => $exchange) {
	// Don't update by default
	$toUpdate = false;

	// If leverage different, set to update
	if(floatval($exchange['leverage']) !== floatval($compositionToSet['leverage'])) {
		$toUpdate = true;
	}

	// If composition different, set to update
	if(array_diff($exchange['compo'], $compositionToSet['compo'])) {
		$toUpdate = true;
		if ($verbose){
			echo "your old allocation was\n";
			echo var_dump(array_diff($exchange['compo'], $compositionToSet['compo']));
		}
	}

	// If composition different, update allocation for this exchange
	if(! $toUpdate) {
		// Log
		echo "Nothing to update for exchange " . $exchanges_names[$exchangeId] . " ". $exchangeId . "\n";
		continue;
	}

	// Rebuild string for composition
	$params = json_encode([
		'botOnly' => $compositionToSet['botOnly'],
		'compo' => [
			'leverage' => strval($compositionToSet['leverage']),
			'compo' => $compositionToSet['compo']
		]
	]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://middle.napbots.com/v1/account/' . $exchangeId);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'token: ' . $authToken]); 
	curl_setopt($ch, CURLOPT_HEADER  , true);
	if (! $dry_run){
		$response = curl_exec ($ch);
		curl_close($ch);
		// Log
		echo "Updated allocation for exchange " .$exchanges_names[$exchangeId] . " ". $exchangeId . "\n";
	}
	else{
		echo "DRY RUN MODE\n";
		echo "nothing was done to your account\n";
	}
}
