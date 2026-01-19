<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

/**
 * File: GoogleStorage
 * 	Configures the AmazonS3 class to point to Google Storage.
 *
 * Version:
 * 	2010.08.29
 *
 * License and Copyright:
 * 	See the included NOTICE.md file for more information.
 *
 * See Also:
 * 	[PHP Developer Center](http://aws.amazon.com/php/)
 */


/*%******************************************************************************************%*/
// CLASS

/**
 * Class: GoogleStorage
 */
class GoogleStorage extends AmazonS3
{
	/**
	 * Method: __construct()
	 * 	The constructor
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	$key - _string_ (Optional) Your Google Storage API Key. If blank, it will look for the <GOOGLE_KEY> constant.
	 * 	$secret_key - _string_ (Optional) Your Google Storage API Secret Key. If blank, it will look for the <GOOGLE_SECRET_KEY> constant.
	 *
	 * Returns:
	 * 	_boolean_ false if no valid values are set, otherwise true.
	 */
	public function __construct($key = null, $secret_key = null)
	{
		// If both a key and secret key are passed in, use those.
		if ($key && $secret_key)
		{
			$this->key = $key;
			$this->secret_key = $secret_key;
		}
		// If neither are passed in, look for the constants instead.
		else if (defined('GOOGLE_KEY') && defined('GOOGLE_SECRET_KEY'))
		{
			$this->key = GOOGLE_KEY;
			$this->secret_key = GOOGLE_SECRET_KEY;
		}

		// Call the parent constructor
		parent::__construct($this->key, $this->secret_key);

		// Set default overrides for this service
		$this->set_hostname('commondatastorage.googleapis.com')
			->allow_hostname_override(false);

		return $this;
	}
}
