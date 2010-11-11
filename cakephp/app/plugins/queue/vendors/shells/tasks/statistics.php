<?php
/**
 * Statistics Task File
 *
 * Copyright (c) 2009 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.2
 *
 * @package    queue
 * @subpackage queue.shells.tasks
 * @copyright  2009 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/davidpersson/queue
 */

/**
 * Statistics Task Class
 *
 * @package    queue
 * @subpackage queue.shells.tasks
 */
class StatisticsTask extends QueueShell {
	var $uses = array('Queue.Job');

	function execute() {
		$this->out('Statistics');
		$this->hr();
		$this->out('Updating every 5 seconds');
		$this->out('Press STRG+C to abort');

		while (true) {
			$result = $this->Job->statistics();
			$this->out('Got:');
			$this->out(var_export($result, true));
			sleep(5);
			$this->hr();
		}
	}
}
?>