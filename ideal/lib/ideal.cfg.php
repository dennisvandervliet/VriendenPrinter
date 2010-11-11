<?php
	
	// Use Rabobank, ABN Amro, ING Bank or Simulator
	define('IDEAL_AQUIRER', 'ING Bank');

	// Use FALSE for NOCACHE, make sure PATH and inner files are writable (This folder should not be accessable for webusers)
	//define('IDEAL_CACHE_PATH', dirname(__FILE__) . '/cache/');

	// Your unique iDEAL Merchant ID
	define('IDEAL_MERCHANT_ID', '005063603');

	// Name of your private certificate file (should be located in IDEAL_SECURE_PATH)
	define('IDEAL_PRIVATE_CERTIFICATE_FILE', 'private.ing.cer'); 

	// Password used to generate private key file
	define('IDEAL_PRIVATE_KEY', 'noodkerk');

	// Name of your private certificate file (should be located in IDEAL_SECURE_PATH)
	define('IDEAL_PRIVATE_KEY_FILE', 'private.ing.key');

	// Default return URL after transaction (Usualy overridden by script)
	define('IDEAL_RETURN_URL', '');

	// Path to your private key & certificate files (This folder should not be accessable for webusers)
	define('IDEAL_SECURE_PATH', dirname(__FILE__) . '/ssl/');

	// Your iDEAL Sub ID
	define('IDEAL_SUB_ID', '3');

	// Use TEST/LIVE mode; true=TEST, false=LIVE
	define('IDEAL_TEST_MODE', false);
	
	
	/*
		// Use Rabobank, ABN Amro, ING Bank or Simulator
	define('IDEAL_AQUIRER', 'Simulator');

	// Use FALSE for NOCACHE, make sure PATH and inner files are writable (This folder should not be accessable for webusers)
	//define('IDEAL_CACHE_PATH', dirname(__FILE__) . '/cache/');

	// Your unique iDEAL Merchant ID
	define('IDEAL_MERCHANT_ID', '123456789');

	// Name of your private certificate file (should be located in IDEAL_SECURE_PATH)
	define('IDEAL_PRIVATE_CERTIFICATE_FILE', 'private.cer'); 

	// Password used to generate private key file
	define('IDEAL_PRIVATE_KEY', 'Password');

	// Name of your private certificate file (should be located in IDEAL_SECURE_PATH)
	define('IDEAL_PRIVATE_KEY_FILE', 'private.key');

	// Default return URL after transaction (Usualy overridden by script)
	define('IDEAL_RETURN_URL', '');

	// Path to your private key & certificate files (This folder should not be accessable for webusers)
	define('IDEAL_SECURE_PATH', dirname(__FILE__) . '/ssl/');

	// Your iDEAL Sub ID
	define('IDEAL_SUB_ID', '0');

	// Use TEST/LIVE mode; true=TEST, false=LIVE
	define('IDEAL_TEST_MODE', true);
*/
?>