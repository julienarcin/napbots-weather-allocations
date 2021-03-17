<?php

// Account details
$email = '';
$password = '';

// simulation mode
$dry_run = true;

// you want to ignore an exchange ? put its name here in CAPITAL
// KRAKEN, "BINANCE" "BITFINEX" "BITMEX" "BITPANDA"

//
// by default ignore no exchange
$exchange_ignore_list = [];
# $exchange_ignore_list = [ 'KRAKEN' ];

$verbose = true;

// Weather dependent compositions
//  - Total of allocations should be equal to 1
//  - Leverage should be between 0.00 and 1.50
//  - How to find bot IDS: https://imgur.com/a/BeV65aO
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


/*
 * Some bot ids on 2021-01-25

--------------- Hourly -------------------
 
STRAT_BTC_ETH_USD_H_1 => NapoX alloc ETH/BTC/USD AR hourly

STRAT_BTC_USD_FUNDING_8H_1 => NapoX BTC Funding AR hourlyY

STRAT_BTC_ETH_USD_LO_H_1 => NapoX alloc ETH/BTC/USD LO hourly

STRAT_ETH_USD_FUNDING_8H_1 => NapoX ETH Funding AR hourly

STRAT_ETH_USD_H_3_V2 => NapoX ETH Ultra flex AR hourly

STRAT_BTC_USD_H_3_V2 => NapoX BTC Ultra flex AR hourly

STRAT_ETH_USD_H_4_V2 => NapoX ETH AR hourly

STRAT_BTC_USD_H_4_V2 => NapoX BTC AR hourly

--------------- Daily -------------------

STRAT_BTC_ETH_USD_LO_D_1 = NapoX alloc ETH/BTC/USD LO daily

STRAT_BTC_ETH_USD_D_1_V2 = NapoX alloc ETH/BTC/USD AR daily

STRAT_BCH_USD_LO_D_1 = NapoX BCH LO daily

 */

?>
