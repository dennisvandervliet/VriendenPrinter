<?php
/**
 * Officeshots.org - Test your office documents in different applications
 * Copyright (C) 2009 Stichting Lone Wolves
 * Written by Sander Marechal <s.marechal@jejik.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

App::import('Vendor', 'BeanStalk.BeanStalk', array('file' => 'beanstalk-1.2.1/src/BeanStalk.class.php'));

/**
 * Queue class (singleton) to interface with beanstalkd
 */
class BeanStalkManager extends Object
{
	/** @var object Singleton instance of BeanStalk */
	private static $beanstalk = null;
	
	/** @var array Default configuration */
	protected static $_baseConfig = array(
		'servers' => array(
			'127.0.0.1:11300'
		),
		'select'                => 'random wait',
		'connection_timeout'    => 0.5,
		'peek_usleep'           => 2500,
		'connection_retries'    => 3,
		'default_tube'       => 'default',
	);

	/** @var array The configuration settings */
	public static $config = array();

	/**
	 * Set the queue configuration and initialise the queue
	 *
	 * @see config/core.default.php for configuration settings
	 * @param array $config Associative array of settings passed to the engine
	 * @return boolean Success
	 */
	public static function config($config = array())
	{
		if (self::$beanstalk) {
			return false;
		}
		
		self::$config = array_merge(self::$_baseConfig, $config);
	}

	/**
	 * Returns a singleton BeanStalk instance
	 *
	 * @return object
	 */
	public static function &getBeanStalk() {
		if (!self::$beanstalk) {
			self::$beanstalk =& BeanStalk::open(self::$config);
			self::$beanstalk->use_tube(self::$config['default_tube']);
		}

		return self::$beanstalk;
	}
}

?>
