# napbots-weather-allocations

## simple
copy the file, change the file config

launch the file

	php manage-allocations.php

install php if you don't have it	

## advanced  usage 
rename "config.example.php" to "config.php"

change config.php settings

then launch this in a shell every time you want to change allocation

	php manage-allocations.php


# NEW 2021-01-26 force mode

	php manage-allocations.php force extreme
	php manage-allocations.php force mild_bull
	php manage-allocations.php force mild_bear


# debug and verbose

	php manage-allocations.php debug 
	php manage-allocations.php verbose 

# dry-run : simulation mode

	php manage-allocations.php dry 
