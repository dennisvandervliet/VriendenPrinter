<?php


  App::import('Core', 'BeanStalk.BeanStalkManager');
 BeanStalkManager::config(array(
  	'servers' => array(
  		'127.0.0.1:11300'
  	),
  	'select'                => 'random wait',
  	'connection_timeout'    => 0.5,
  	'peek_usleep'           => 2500,
  	'connection_retries'    => 3,
 	'default_tube'          => 'default',
	
 ));
 
Configure::write('Worker.pidfile', '/home/fb/test.pid');


?>
