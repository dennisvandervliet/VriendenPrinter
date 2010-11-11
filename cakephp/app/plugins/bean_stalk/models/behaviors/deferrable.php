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

App::import('Core', 'BeanStalk.BeanStalkManager');

/**
 * The Deferrable behaviour allows calling model methods from a background
 * process.
 */
class DeferrableBehavior extends ModelBehavior
{
	/** Reference to the BeanStalk singleton */
	private static $beanstalk = null;

	/** @var string The settings for deferred calls */
	public $deferred_args = array();

	/**
	 * Setup of the behavior
	 *
	 * @param object $model A reference to the model
	 * @param string $tube The name of the tube to use by default for deferred calls
	 */
	public function setup(&$Model, $deferred_args = array())
	{
		if (!isset($this->deferred_args[$Model->alias])) {
			$this->deferred_args[$Model->alias] = array(
				'priority' => 1024,
				'delay'    => 0,
				'ttr'      => 120,
				'tube'     => BeanStalkManager::$config['default_tube'],
			);
		}

		if (!$deferred_args) {
			$deferred_args = array();
		}

		if ($deferred_args && !is_array($deferred_args)) {
			$deferred_args = array('tube' => $deferred_args);
		}

		$this->deferred_args[$Model->alias] = array_merge($this->deferred_args[$Model->alias], $deferred_args);
	}

	/**
	 * Defer a method call
	 *
	 * @param object $model A reference to the model
	 * @param string $method The method to call
	 * @param array $args The arguments for the method call
	 * @param array $args The arguments for the deferred itself
	 * @return boolean Success
	 */
	public function defer(&$Model, $method, $args = array(), $deferred_args = array())
	{
		if (!self::$beanstalk) {
			self::$beanstalk =& BeanStalkManager::getBeanStalk();
		}

		if (!is_array($args)) {
			$args = array($args);
		}

		$deferred_args = array_merge($this->deferred_args[$Model->alias], $deferred_args);
		$message = array(
			'type'   => 'deferred',
			'Model'  => $Model->name,
			'id'     => $Model->id,
			'data'   => $Model->data,
			'method' => $method,
			'args'   => $args
		);

		extract($deferred_args);
		$result = self::$beanstalk->put($priority, $delay, $ttr, serialize($message), $tube);

		if ($result != BeanQueue::OPERATION_OK) {
			$this->log('Deferring ' . $Model->name . '->' . $method . ' failed with error code ' . $result);
			return false;
		}

		$this->log('Deferred ' . $Model->name . "->$method to tube '$tube' with ID " . self::$beanstalk->last_insert_id(), LOG_DEBUG);
		return true;
	}
}

?>
