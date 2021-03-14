# napbots-weather-allocations
This code is a fork of https://github.com/julienarcin/napbots-weather-allocations

## French tutorial to use this script on a Synology NAS
Tutoriel mise en place script sur NAS Synology
https://docs.google.com/document/d/1aGmp8jxQC8DUtcthfg0MwbMG18sfWGx7mHMdRnmQmbA/edit?usp=sharing

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

# force mode

	php manage-allocations.php force extreme
	php manage-allocations.php force mild_bull
	php manage-allocations.php force mild_bear

# debug and verbose

	php manage-allocations.php debug 
	php manage-allocations.php verbose 

# dry-run : simulation mode

	php manage-allocations.php dry 
