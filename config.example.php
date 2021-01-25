<?php

// Account details
$email = '';
$password = '';
$userId = 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'; // How to find userId: https://imgur.com/a/fW4I8Be

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
 * some ids on 2021-01-25
STRAT_BTC_ETH_USD_H_1 NapoX alloc ETH/BTC/USD AR hourly	1,333.44%	HOURLY

STRAT_BTC_USD_FUNDING_8H_1 NapoX BTC Funding AR hourly	529.78%	HOURLY

STRAT_BTC_ETH_USD_LO_H_1 NapoX alloc ETH/BTC/USD LO hourly	650.87%	HOURLY

STRAT_ETH_USD_FUNDING_8H_1 NapoX ETH Funding AR hourly	324.61%	HOURLY

STRAT_ETH_USD_H_3_V2 NapoX ETH Ultra flex AR hourly	674.24%	HOURLY

STRAT_BTC_USD_H_3_V2 NapoX BTC Ultra flex AR hourly	324.65%	HOURLY

STRAT_ETH_USD_H_4_V2 NapoX ETH AR hourly	364.87%	HOURLY

STRAT_BTC_USD_H_4_V2	NapoX BTC AR hourly	398.74%	HOURLY

--------------- daily -------------------

STRAT_BTC_ETH_USD_LO_D_1 = NapoX alloc ETH/BTC/USD LO daily	653.95%	DAILY

STRAT_BTC_ETH_USD_D_1_V2 = NapoX alloc ETH/BTC/USD AR daily	561.76%	DAILY

STRAT_BCH_USD_LO_D_1 = NapoX BCH LO daily	23.52%	DAILY

 */
