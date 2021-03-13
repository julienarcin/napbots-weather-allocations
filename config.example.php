<?php

// Account details
$email = '';
$password = '';

// simulation mode
$dry_run = false;
// verbose mode
$verbose = true;
// debug mode (very verbose)
$debug = false;

// by default ignore no exchange
$exchange_ignore_list = [];
# $exchange_ignore_list = [ 'KRAKEN' ];

// Weather dependent compositions
//  - Total of allocations should be equal to 1
//  - Leverage should be between 0.00 and 1.50
$compositions = [
	'mild_bear' => [
		'compo' => [
			'NapoX BTC Funding AR hourly' => 0.15,
			'NapoX ETH Funding AR hourly' => 0.15,
			'NapoX alloc ETH/BTC/USD AR hourly' => 0.70,
		],
		'leverage' => 1.0,
		'botOnly' => true
	],
	'mild_bull' => [
		'compo' => [
			'NapoX BTC Funding AR hourly' => 0.25,
			'NapoX ETH Funding AR hourly' => 0.25,
			'NapoX alloc ETH/BTC/USD AR hourly' => 0.50,
		],
		'leverage' => 1.5,
		'botOnly' => true
	],
	'extreme' => [
		'compo' => [
			'NapoX ETH Ultra flex AR hourly' => 0.4,
			'NapoX BTC Ultra flex AR hourly' => 0.4,
			'NapoX alloc ETH/BTC/USD AR hourly' => 0.2,
		],
		'leverage' => 1.0,
		'botOnly' => true
	],
	// It is possible to backup other unused allocations in order not to loose them.
	// Feel free to add as many allocations (backup1, backup2, doesnotwork, ...) as you want
	'backup' => [
		'compo' => [
			'NapoX alloc ETH/BTC/USD AR hourly' => 0.35,
			'NapoX BTC Funding AR hourly' => 0.25,
			'NapoX alloc ETH/BTC/USD LO hourly' => 0.14,
			'NapoX alloc ETH/BTC/USD LO daily' => 0.13,
			'NapoX BTC AR hourly' => 0.13
		],
		'leverage' => 1.0,
		'botOnly' => true
	],
	'test mild_bear' => [
		'compo' => [
			'NapoX BTC AR hourly' => 0.15,
		//	'NapoX BTC Funding AR hourly' => 0.00,
			'NapoX BTC Volume AR daily' => 0.15,
			'NapoX ETH AR hourly' => 0.10,
			'NapoX ETH Funding AR hourly' => 0.10,
			'NapoX ETH Volume AR daily' => 0.15,
			'NapoX alloc ETH/BTC/USD AR hourly' => 0.35 // AR or LO
		],
		'leverage' => 1.00,
		'botOnly' => true
	],
	'test mild_bull' => [
		'compo' => [
			'NapoX BTC Funding AR hourly' => 0.20,
			'NapoX BTC Ultra flex AR hourly' => 0.05,
			'NapoX BTC Volume AR daily' => 0.20,
			'NapoX ETH Funding AR hourly' => 0.20,
			'NapoX ETH Ultra flex AR hourly' => 0.05,
			'NapoX ETH Volume AR daily' => 0.20,
			'NapoX alloc ETH/BTC/USD AR daily' => 0.10 // AR or LO
		],
		'leverage' => 1.35,
		'botOnly' => true
	],
	'test extreme' => [
		'compo' => [
			'NapoX BTC AR hourly' => 0.10,
			'NapoX BTC Ultra flex AR hourly' => 0.10,
			'NapoX ETH AR hourly' => 0.10,
			'NapoX ETH Ultra flex AR hourly' => 0.10,
			'NapoX alloc ETH/BTC/USD AR daily' => 0.20,
			'NapoX alloc ETH/BTC/USD AR hourly' => 0.40 // AR or LO
		],
		'leverage' => 1.10,
		'botOnly' => true
	]
];

/*
 * Liste of all allocations 2021-03-11
 * from https://middle.napbots.com/v1/strategy
 * 
 * 'NapoX medium term TF ETH LO'
 * 'NapoX ETH Volume AR daily'
 * 'NapoX BNB LO daily'
 * 'NapoX BTC Volume AR daily'
 * 'NapoX medium term TF BTC LO'
 * 'NapoX BTC Funding AR hourly'
 * 'NapoX alloc ETH/BTC/USD AR daily'
 * 'NapoX BTC Ultra flex AR hourly'
 * 'NapoX ETH AR daily'
 * 'NapoX BTC AR hourly'
 * 'NapoX medium term TF EOS LO'
 * 'NapoX alloc ETH/BTC/USD AR hourly'
 * 'NapoX ETH Funding AR hourly'
 * 'NapoX medium term TF LTC LO'
 * 'NapoX ETH AR hourly'
 * 'NapoX alloc ETH/BTC/USD LO hourly'
 * 'NapoX ETH Ultra flex AR hourly'
 * 'NapoX BTC AR daily'
 * 'NapoX BCH LO daily'
 * 'NapoX medium term TF XRP LO'
 * 'NapoX alloc ETH/BTC/USD LO daily'
 * 
 */


// https://drive.google.com/file/d/1DOw5c268liGiLXZLtVLpP5B9go1gyezn/view
// https://pipedream.com/@gahabeen/napbots-dynamic-allocations-v1-p_ZJCbgy2/readme

?>
