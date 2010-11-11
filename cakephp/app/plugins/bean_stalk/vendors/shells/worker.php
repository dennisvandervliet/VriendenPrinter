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
 * An autonomous worker process to execute deferred model calls
 */
class WorkerShell extends Shell
{
	/** @var object Reference to the BeanStalk queue */
	private $beanstalk = null;

	/** @var int Background proccess PID */
	public $pid = null;

	/** @var array A queue for cron jobs. Note that it is reverse sorted. */
	public $cron = array();

	/**
	 * Override startup to parse arguments
	 */
	public function startup()
	{
		include_once("/home/fb/cakephp/app/plugins/bean_stalk/config/core.default.php");
		$this->tubes = array(BeanStalkManager::$config['default_tube']);
		if (!empty($this->params['tubes'])) {
			$this->tubes = explode(',', $this->params['tubes']);
		}
	}

	/**
	 * Main function. Print help and exit.
	 */
	public function main()
	{
		$this->help();
		$this->_stop();
	}

	/**
	 * Get the status of the running worker process
	 */
	public function status()
	{
		$this->beanstalk =& BeanStalkManager::getBeanStalk();

		// Watch the incoming tube
		$tube_out = trim(array_shift($this->tubes));
		$tube_in  = $tube_out . '-status';
		$this->beanstalk->watch($tube_in);

		// Send a status request to the worker
		$message = array(
			'type' => 'status',
			'tube' => $tube_in,
		);
		$this->beanstalk->put(0, 0, 30, serialize($message), $tube_out);

		while (true) {
			// Wait for a response with a timeout slightly longer than the TTR
			$job = $this->beanstalk->reserve_with_timeout(35);

			if ($job) {
				$job_id = $job->get_jid();

				// Make sure we're not reading an old status message
				if ($job_id < $this->beanstalk->last_insert_id()) {
					continue;
				}

				$log_message = sprintf('Got job ID %s from beanstalkd', $job_id);
				$this->log($log_message, LOG_DEBUG);
				
				$message = unserialize($job->get());
				$job->delete();

				$this->out(print_r($message, true));
				$this->_stop();
			}

			$log_message = 'Worker not responding';
			$this->log($log_message, LOG_ERROR);
			$this->out($log_message);
			$this->_stop();
		}
	}

	/**
	 * Stop the running worker
	 */
	public function stop()
	{
		$this->beanstalk =& BeanStalkManager::getBeanStalk();
		$this->out('Stopping CakePHP background worker');
		
		// First, check if the daemon is actually running
		$pidfile = Configure::read('Worker.pidfile');
		if (!file_exists($pidfile)) {
			$this->out('The background worker is not running.');
			$this->_stop();
		}

		$pid = trim(file_get_contents($pidfile));
		if (posix_kill($pid, 0) || posix_get_last_error() <= 1) {
			// Dameon is running. Send a stop signal.
			$message = array('type' => 'stop');
			$tube = trim(array_shift($this->tubes));
			$this->beanstalk->put(0, 0, 30, serialize($message), $tube);

			// Exit
			$this->_stop();
		}

		$this->out('The background worker is not running. Removing stale pidfile');
		@unlink($pidfile);
	}

	/**
	 * Run the worker process
	 */
	public function run()
	{
		$this->out('Starting CakePHP background worker');
		//$this->daemonize();
		$this->beanstalk =& BeanStalkManager::getBeanStalk();
		// Start watching Beanstalkd tubes

		foreach ($this->tubes as $tube) {
			$tube = trim($tube);
			$this->beanstalk->watch($tube);
		}

		// Initialize the cron queue. We don't know the last run, so run everything at startup
		$tasks = Configure::read('Cron');

		if (is_array($tasks)) {
			foreach ($tasks as $task_name => $task) {
				$this->cron[time()][] = $task_name;
			}
	
			reset($this->cron);
		}

		// Main loop
		while (true) {	
			
			// Execute pending cron jobs. This gives the time to the next planned job
			$time = $this->cron_tick();
			//echo "test point 1\n";
			$this->log('Next cron job in ' . $time . ' seconds', LOG_DEBUG);
			//echo "test point 2\n";
			$job = $this->beanstalk->reserve_with_timeout(30);
			//echo "test point 3\n";
			if ($job == false) {
				// We get here when we get a bogus message, on DEALINE_SOON and on TIMEOUT
				sleep(15);
				echo "no jobs\n\r";
				continue;
			}

			$log_message = sprintf('Got job ID %s from beanstalkd', $job->get_jid());
			//echo $log_message . "\n\r";
			$this->log($log_message, LOG_DEBUG);

			$message = $job->get();
			//int_r($message);
			$method  = 'run_' . $message['type'];
			//echo $method;
			
			
			if (method_exists($this, $method)) {
				call_user_func(array($this, $method), $message);
			} else {
				$this->__exec($message);
			}
			$job->delete();
			
			
		}
	}

	/**
	 * Daemonize this shell
	 */
	private function daemonize()
	{
		if (isset($this->params['nofork'])) {
			return;
		}

		// First, check if we are allowed to write the pidfile. If not, exit with
		// an error before we fork into the background
		$pidfile = Configure::read('Worker.pidfile');

		if (!is_writable(dirname($pidfile)) || (file_exists($pidfile) && !is_writable($pidfile))) {
			$this->out("Cannot write pidfile to $pidfile. Permission denied.");
			$this->_stop();
		}

		// Check if a worker is already running
		if (file_exists($pidfile)) {
			$pid = trim(file_get_contents($pidfile));

			if (posix_kill($pid, 0) || posix_get_last_error() <= 1) {
				$this->out("Worker already running with PID: $pid");
				$this->_stop();
			}

			$this->out('Removing stale pidfile');
			@unlink($pidfile);
		}

		// Do the unix double-fork magic. See Stevens' "Advanced
		// Programming in the UNIX Environment" for details (ISBN 0201563177)
		// http://www.erlenstar.demon.co.uk/unix/faq_2.html#SEC16

		// First fork
		$pid = pcntl_fork();
		if ($pid != 0) {
			if ($pid == -1) {
				$this->out('Fork #1 failed');
			}
			$this->_stop();
		}

		// Decouple from parent environment
		chdir('/');
		posix_setsid();
		@umask(0);

		// Second fork
		$pid = pcntl_fork();
		if ($pid != 0) {
			if ($pid == -1) {
				$this->out('Fork #2 failed');
			}
			$this->_stop();
		}

		// Close file descriptors
		fclose(STDIN);
		fclose(STDOUT);
		fclose(STDERR);

		// Open three new file descripts. These will magically take the place of STDIN, STDOUT and STDERR
		$this->Dispatch->stdin  = fopen('/dev/null', 'r');
		$this->Dispatch->stdout = fopen('/dev/null', 'w');
		$this->Dispatch->stderr = fopen('/dev/null', 'w');

		// Write pidfile and register deletion on exit
		$this->pid = posix_getpid();
		file_put_contents($pidfile, $this->pid);
		register_shutdown_function(array($this, 'delpid'));
	}

	/**
	 * Delete the pid file
	 * Needs to be public so it can be registered with register_shutdown_function()
	 */
	public function delpid()
	{
		@unlink(Configure::read('Worker.pidfile'));
	}

	/**
	 * Execute a deferred model call
	 * @param array $deferred The deferred message from beanstalkd
	 */
	private function run_deferred($deferred)
	{
		App::import('Model', $deferred['Model']);
		$model = ClassRegistry::init($deferred['Model']);
		$model->create();
		$model->id = $deferred['id'];
		$model->data = $deferred['data'];
		$model->read();
		call_user_func_array(array($model, $deferred['method']), $deferred['args']);

		$message = sprintf('Processed %s->%s with ID %s', $deferred['Model'], $deferred['method'], $deferred['id']);
		$this->log($message, LOG_DEBUG);
		$this->out($message);
		
		unset($model);
	}

	/**
	 * Output the worker status to beanstalkd
	 * @param array $message The message from beanstalkd
	 */
	private function run_status($message)
	{
		// Get the status from the beanstalkd servers
		$stats = '';
		$this->beanstalk->stats($stats);

		// Post the result back to the process that requested it
		$reply = array(
			'pid' => posix_getpid(),
			'tubes' => $this->tubes,
			'queue_stats' => $stats,
		);

		$this->beanstalk->put(0, 0, 30, serialize($reply), $message['tube']);
	}

	/**
	 * Stop the background daemon
	 */
	private function run_stop($message)
	{
		$this->_stop();
	}

	/**
	 * Look at the cron queue, execute any outstanding tasks
	 * Note that $this->cron is sorted in reversed. This is because array_shift renumbers
	 * the keys while array_pop does not.
	 * @return int Time in seconds until the next task
	 */
	private function cron_tick()
	{
		if (sizeof($this->cron) == 0) {
			return 3600; // Arbitrary
		}

		$time = time();
		end($this->cron);

		while (key($this->cron) <= $time) {
			// Read the task
			$task_names = array_pop($this->cron);
			foreach ($task_names as $task_name) {
				$task = Configure::read('Cron.' . $task_name);

				// Execute the task
				App::import('Model', $task['Model']);
				$model = ClassRegistry::init($task['Model']);
				$model->create();

				if (!method_exists($model, $task['method'])) {
					$message = sprintf('%s->%s does not exist', $task['Model'], $task['method']);
					$this->log($message, LOG_DEBUG);
					$this->out($message);
				}

				if (isset($task['args'])) {
					call_user_func_array(array($model, $task['method']), $task['args']);
				} else {
					call_user_func(array($model, $task['method']));
				}
				unset($model);

				$message = sprintf('Executed cron %s->%s', $task['Model'], $task['method']);
				$this->log($message, LOG_DEBUG);
				$this->out($message);

				// Reschedule
				$start = time() + $task['interval'];
				$this->cron[$start][] = $task_name;
				krsort($this->cron, SORT_STRING);
				end($this->cron);
			}

			// Advance time
			$time = time();
		}

		// The time until the next job
		return key($this->cron) - $time;
	}

	/**
	 * Print help and exit
	 */
	public function help()
	{
		$this->out('Cake Background Worker.');
		$this->hr();
		$this->out("Usage: cake worker <command> <arg1>...");
		$this->hr();
		$this->out('Commands:');
		$this->out("\n\trun [nofork] [tubes tube1,...]\n\t\tRun the worker");
		$this->out("\n\tstop\n\t\tStop the worker");
		$this->out("\n\tstatus\n\t\tShow status of the running worker");
		$this->out("\n\thelp\n\t\tShow this help");
		$this->out('Params:');
		$this->out("\n\tnofork\n\t\tDo not fork into the background");
		$this->out("\n\ttubes\n\t\tA comma separated list of BeanStalk tubes to watch");
		$this->out('');
	}
	
	private function __exec($command) {
		echo $command;
		echo "\n\r" ;
		$test = shell_exec($command);
		echo "pid" . $test;
		echo "\n\r" ;
		
	}
}

?>
