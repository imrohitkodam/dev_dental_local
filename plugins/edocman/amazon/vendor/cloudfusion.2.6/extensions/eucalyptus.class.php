<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

/**
 * File: Eucalyptus
 * 	Configures the AmazonEC2 class to point to Eucalyptus Community Cloud.
 *
 * Version:
 * 	2010.08.22
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
 * Class: Eucalyptus
 */
class Eucalyptus extends AmazonEC2
{
	/**
	 * Method: __construct()
	 * 	The constructor
	 *
	 * Access:
	 * 	public
	 *
	 * Parameters:
	 * 	$key - _string_ (Optional) Your Eucalyptus API Key. If blank, it will look for the <EUCALYPTUS_KEY> constant.
	 * 	$secret_key - _string_ (Optional) Your Eucalyptus API Secret Key. If blank, it will look for the <EUCALYPTUS_SECRET_KEY> constant.
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
		else if (defined('EUCALYPTUS_KEY') && defined('EUCALYPTUS_SECRET_KEY'))
		{
			$this->key = EUCALYPTUS_KEY;
			$this->secret_key = EUCALYPTUS_SECRET_KEY;
		}

		// Call the parent constructor
		parent::__construct($this->key, $this->secret_key);

		// Set default overrides for this service
		$this->set_hostname('ecc.eucalyptus.com', '8773')
		   ->set_resource_prefix('/services/Eucalyptus')
		   ->allow_hostname_override(false);

		return $this;
	}
}
