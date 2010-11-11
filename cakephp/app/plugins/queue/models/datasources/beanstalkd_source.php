<?php
/**
 * Beanstalkd Source File
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
 * @subpackage queue.models.datasources
 * @copyright  2009 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/davidpersson/queue
 */
App::import('Core', 'DataSource');
App::import('Lib', 'Queue.BeanstalkdSocket');

/**
 * Beanstalkd Source Class
 *
 * @package    queue
 * @subpackage queue.models.datasources
 */
class BeanstalkdSource extends DataSource {

/**
 * Holds ID of last inserted job
 *
 * Works analog to {@see Model::__insertID}.
 *
 * @var mixed
 * @access private
 */
	var $__insertID;

/**
 * The default configuration of a specific DataSource
 *
 * @var array
 * @access public
 */
	var $_baseConfig = array(
		'host' => '85.147.2550.2161',
		'port' => 11300,
		'ttr' => 120,
		'kickBound' => 100,
		'format' => 'php'
	);

	function __construct($config = array()) {
		parent::__construct();
		$this->setConfig($config);
		$this->fullDebug = Configure::read('debug') > 1;
		$this->connection = new BeanstalkdSocket($this->config);
		$this->connected =& $this->connection->connected;
		$this->connect();
	}

	function close() {
		if ($this->connected) {
			$this->disconnect();
		}
	}

	function connect() {
		if (!$this->connection->connect()) {
			$error = $this->lastError();
			trigger_error("BeanstalkdSource - Could not connect. Error given was '{$error}'.", E_USER_WARNING);
			return false;
		}
		return true;
	}

	function disconnect() {
		return $this->connection->disconnect();
	}

	function isConnected() {
		return $this->connected;
	}

	function listtubes($type) {

		$id = $this->connection->listTubes($this->type);
		return $id;
		
	
	}
	
	function lt() {
		return $this->connection->lt();
	}
	
	function put(&$Model, $data, $options = array()) {
		$Model->set($data);
		$body = $Model->data[$Model->alias];

		$priority = 0;
		$delay = 0;
		$ttr = $this->config['ttr'];
		$tube = 'default';
		extract($options, EXTR_OVERWRITE);

		if (!$this->choose($Model, $tube)) {
			return false;
		}
		$id = $this->connection->put($priority, $delay, $ttr, $this->_encode($body));
		
		if ($id !== false) {
			$Model->setInsertId($id);
			return $this->__insertID = $Model->id = $id;
		}
		return false;
	}

	function choose(&$Model, $tube) {
		return $this->connection->choose($tube) === $tube;
	}

	function reserve(&$Model, $options = array()) {
		$timeout = null;
		$tube = null;
		extract($options, EXTR_OVERWRITE);

		if ($tube && !$this->watch($Model, $tube)) {
			return false;
		}
		if (!$result = $this->connection->reserve($timeout)) {
			return false;
		}
		$data = $this->_decode($result['body']);
		$data['id'] = $result['id'];
		return $Model->set(array($Model->alias => $data));
	}

	function watch(&$Model, $tube) {
		foreach ((array)$tube as $t) {
			if (!$this->connection->watch($t)) {
				return false;
			}
		}
		return true;
	}

	function release(&$Model, $options = array()) {
		if (!is_array($options)) {
			$options = array('id' => $options);
		}
		$id = null;
		$priority = 0;
		$delay = 0;
		extract($options, EXTR_OVERWRITE);

		if ($id === null) {
			$id = $Model->id;
		}
		return $this->connection->release($id, $priority, $delay);
	}

	function touch(&$Model, $options = array()) {
		if (!is_array($options)) {
			$options = array('id' => $options);
		}
		$id = null;
		extract($options, EXTR_OVERWRITE);

		if ($id === null) {
			$id = $Model->id;
		}
		return $this->connection->touch($id);
	}

	function bury(&$Model, $options = array()) {
		if (!is_array($options)) {
			$options = array('id' => $options);
		}
		$id = null;
		$priority = 0;
		extract($options, EXTR_OVERWRITE);

		if ($id === null) {
			$id = $Model->id;
		}
		return $this->connection->bury($id, $priority);
	}

	function kick(&$Model, $options = array()) {
		if (!is_array($options)) {
			$options = array('bound' => $options);
		}
		$bound = $this->config['kickBound'];
		$tube = null;
		extract($options, EXTR_OVERWRITE);

		if ($tube && !$this->choose($Model, $tube)) {
			return false;
		}
		return $this->connection->kick($bound);
	}

	function peek(&$Model, $options = array()) {
		if (!is_array($options)) {
			$options = array('id' => $options);
		}
		$id = null;
		extract($options, EXTR_OVERWRITE);

		if ($id === null) {
			$id = $Model->id;
		}
		return $this->connection->peek($id);
	}

	function statistics(&$Model) {
		return $this->connection->stats();
	}

	function _encode($data) {
		switch ($this->config['format']) {
			case 'json':
				return json_encode($data);
			case 'php':
			default:
				//debug(strlen(serialize($data)));
				return serialize($data);
		}
	}

	function _decode($data) {
		switch ($this->config['format']) {
			case 'json':
				return json_decode($data);
			case 'php':
			default:
				return unserialize($data);
		}
	}
	


/**
 * All cal ls to methods on the model are routed through this method
 *
 * @param mixed $method
 * @param mixed $params
 * @param mixed $Model
 * @access public
 * @return void
 */
	function query($method, $params, &$Model) {
		array_unshift($params, $Model);

		$startQuery = microtime(true);

		switch ($method) {
			case 'put':
			case 'listTubes':
				$this->type = $params[1];
				//FireCake::info($params, 'par');
				
			case 'statsTube':
			case 'choose':
			case 'reserve':
			case 'watch':
			case 'release':
			case 'delete':
			case 'touch':
			case 'bury':
			case 'kick':
			case 'peek':
			case 'lt':
			case 'statistics':
				$result = $this->dispatchMethod($method, $params);
				$this->took = microtime(true) - $startQuery;
				$this->error = $this->lastError();
				$this->logQuery($method, $params);
				return $result;
			default:
				trigger_error("BeanstalkdSource::query - Unkown method {$method}.", E_USER_WARNING);
				return false;
		}
	}

	function create(&$Model, $fields = null, $values = null) {
		return false;
	}

	function read(&$Model, $queryData = array()) {
		if ($queryData['fields'] == 'count') {
			if ($this->peek($Model, $queryData['conditions']['Job.id'])) {
				return array(0 => array(0 => array('count' => 1)));
			}
		}
		return false;
	}

	function update(&$Model, $fields = null, $values = null) {
		return false;
	}

/**
 * Deletes a job
 *
 * @param Model $Model
 * @param mixed $id
 */
	function delete(&$Model, $id = null) {
		if ($id == null) {
			$id = $Model->id;
		}
		return $this->connection->delete($id);
	}

/**
 * Returns a data source specific expression
 *
 * @see Model::delete, Model::exists, Model::_findCount
 * @param mixed $model
 * @param mixed $function I.e. `'count'`
 * @param array $params
 * @access public
 * @return void
 */
	function calculate(&$Model, $function, $params = array()) {
		return $function;
	}

/**
 * Returns available sources
 *
 * @see Mode::useTable
 * @return array
 */
	function listSources($data = null) {
		return array('jobs');
	}

	function describe($model) {
	}

	function logQuery($method, $params) {
		$this->_queriesCnt++;
		$this->_queriesTime += $this->took;
		$this->_queriesLog[] = array(
			'query' => $method,
			'error' => $this->error,
			'took' => $this->took,
			'affected' => 0,
			'numRows' => 0
		);
	}

	function lastError() {
		return array_pop($this->connection->errors());
	}

/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * Neeed as as workaround for beanstalkd's missing last insert id support.
 *
 * @param unknown_type $source
 * @return integer
 */
	function lastInsertId($source = null) {
		return $this->__insertID;
	}
}
?>