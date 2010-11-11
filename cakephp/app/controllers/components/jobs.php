<?
class JobsComponent extends Object {
var $q;
var $t;
var $wwwBase = "http://www.vriendenprinter.nl/";


function _directoryJob($photo, $facebookid, $example, $callback = NULL, $sizes = array(), $real = NULL) {
	//echo $facebookid;
	$payload = array(
			'array' => $photo,
			'facebookId' => $facebookid,
			'type' => 'dir',
			'callBackUrlSuccess' => $callback,
			'sizes' => $sizes);
		if($real == true){
			$tube = "mon";
		}	else {
			$tube = "img";
		}
			
		$options = array('tube' => $this->_allocateTube($tube));
		//$this->q = ClassRegistry::init('Queue.Job');
		$d = $this->q->put($payload, $options); 
		//FireCake::info($options, "jobinfo");
		
		
}	



function _allocateTube ($type = 'gen') {
// find a running tube that can be used to put the job in
// if no tube is found a new tube name is returned
	if ($this->q) {
		return $this->t;
	} else {
		$this->q = ClassRegistry::init('Queue.Job');
		$this->t = $this->q->listTubes($type);
		
		if (empty($this->t)) {
			if ($type == "img") {
				$this->cakeError("noImgWorker");
			}
			$this->t = $type . "-" . uniqid();
			return $this->t;
		}
		return $this->t;	
	}

}
function _bigJob($cmd, $callback = NULL, $example = NULL, $facebookid = NULL, $wallpaper = false) {
// simple function to load a command into the queue
// used to schedule image magick commands

	if ($cmd == '') {
	} else {
	
		$payload = array(
	 'callBackUrlSuccess' => $callback,
	 'cmd' => $cmd,
	 'type' => 'exec',
	 'low' => true);
	 
	if ($wallpaper == true){
	
		$tube = "wal";
	} else {
	
		$tube = "mon";
	}
	if ($example) {
	
		$payload["exampleId"] = $example;
		$payload["facebookId"] = $facebookid;
	}
		$options = array('tube' => $this->_allocateTube($tube));

		$this->q->put($payload, $options); 

	}
}





function _queueJob($cmd, $callback = NULL, $example = NULL, $facebookid = NULL) {
// simple function to load a command into the queue
// used to schedule image magick commands

	if ($cmd == '') {
	} else {
	
		$payload = array(
	 'callBackUrlSuccess' => $callback,
	 'cmd' => $cmd,
	 'type' => 'exec');
	 
	if ($example) {
	
		$payload["exampleId"] = $example;
		$payload["facebookId"] = $facebookid;
	}
		$options = array('tube' => $this->_allocateTube('img'));

		$this->q->put($payload, $options); 

	}
}

function _imageJob($photo, $facebookid, $callback = NULL) {

		$payload = array(
			'array' => $photo,
			'facebookId' => $facebookid,
			'type' => 'image',
			'callBackUrlSuccess' => $callback);
			
		$options = array('tube' => $this->_allocateTube('dow'));
			
		//$this->q = ClassRegistry::init('Queue.Job');
		$this->q->put($payload, $options); 
		
		
		//FireCake::info($options, "jobinfo");
		

}
function _thumbnailJob($photo, $facebookid, $callback = NULL) {

		$payload = array(
			'thumbnails' => $photo,
			'facebookId' => $facebookid,
			'type' => 'thumb',
			'callBackUrlSuccess' => $callback);
			
		$options = array('tube' => $this->_allocateTube('dow'));
			
		//$this->q = ClassRegistry::init('Queue.Job');
		$this->q->put($payload, $options); 
		
		
		//FireCake::info($options, "jobinfo");
		

}

function _ftpJob($files, $callback = NULL) {

		$payload = array(
			'files' => $files,
			'type' => 'ftp',
			'callBackUrlSuccess' => $callback);
			
		$options = array('tube' => $this->_allocateTube('ftp'));
			
		//$this->q = ClassRegistry::init('Queue.Job');
		$this->q->put($payload, $options); 
		
		
		//FireCake::info($options, "jobinfo");
		

}

function _updateStatus($model, $id, $status, $secret = NULL, $do = false) {
	if ($model == NULL) {
			$callback = NULL;
	} else {
		$callback = $this->wwwBase . $model . "s/confirm/" . $id . "/" . $status . "/" . $secret;
	}

	return $callback;
}	

}

?>