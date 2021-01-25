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

$verbose = false;
$dry_run = false;
$exchange_ignore_list = [];

if (file_exists('config.php')){
	include 'config.php';
}


// Get crypto weather
$weatherApi = file_get_contents('https://middle.napbots.com/v1/crypto-weather');
if($weatherApi) {
	$weather = json_decode($weatherApi,true)['data']['weather']['weather'];
}
// Find composition to set
if($weather === 'Extreme markets') {
	$compositionToSet = $compositions['extreme'];
} elseif($weather === 'Mild bull markets'){
	$compositionToSet = $compositions['mild_bull'];
} elseif($weather === 'Mild bear or range markets') {
	$compositionToSet = $compositions['mild_bear'];
} else {
	throw new \Exception('Invalid crypto-weather: ' . $weather);
}


function check_compositions($compositions)
{
	global $verbose;
	foreach($compositions as $weather => $composition) {
		if (true == $verbose) echo("[$weather]\n");
		$sum = 0.0;
		foreach($composition['compo'] as $val){
			if (true == $verbose) echo("add $val\n");
			$sum = floatval($val) + $sum;
		}
		if (true == $verbose) echo($sum."\n");
		if (1 != $sum){
			throw new \Exception("sum of you allocations for [$weather] is [$sum] it should be 1.0, check your numbers.");
		}
	}
}

check_compositions($compositions);

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
		if (true == $verbose){
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
	}
	else{
		echo "DRY RUN MODE\n";
		echo "nothing was done to your account\n";
	}

	// Log
	echo "Updated allocation for exchange " .$exchanges_names[$exchangeId] . " ". $exchangeId . "\n";
}
