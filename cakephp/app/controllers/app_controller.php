<?
App::import('Vendor', 'DebugKit.FireCake'); 
App::import('Lib', 'Facebook.FB');
App::import('Helper', 'Facebook.Facebook');
App::import('Vendor', 'BeanStalk.BeanStalk', array('file' => 'beanstalk-1.2.1/src/BeanStalk.class.php'));

class AppController extends Controller {

var $components = array('Facebook.Connect' => array('createUser' => false) , 'Session',  'RequestHandler', 'Email', 'Montage', "Jobs");
var $helpers = array('Facebook.Facebook', 'Form', 'Javascript', 'Ajax', 'Number', 'Cache', "Time");
var $facebookInfo;
var $uses = array('Online.Online', 'Twitter');

function beforeFilter (){

	if ($this->Connect->me){
		
		$this->facebookInfo = $this->Connect->me;
		
		$this->set("facebookInfo", $this->Connect->me);
		
		//$this->Session->setFlash("ok", 'notice');
		
	} else {
	
		$facebookInfo["id"] = false;
		
		$this->set("facebookInfo", $facebookInfo);
		
		//$this->Session->setFlash("not ok", 'notice');
		
		
		if($this->action == "confirm" OR $this->action == "index"  OR $this->action == "sleep" OR $this->action == "stress" OR $this->action == "detail" OR $this->action == "pdf" OR $this->action == "display" OR $this->action == "home") {
		
		} else {
		
			$this->redirect("/posters/index/");
		}

	
	}
	/*if($this->Connect->me OR $this->action == "confirm"){
		//$this->set('fb',array(
			//'login' => $this->Connect->me));

	} else {
		//FireCake::info($this->action, "debug");
		$this->redirect("/posters/index");
		exit;
	}
	*/
	
	
	if ($this->RequestHandler->isAjax()) {
	} else {
		$this->layout = 'printer';
	}
	
	if(Configure::read('debug') <= 1) {
	
	FireCake::disable();
	}
	
	if($this->action == 'confirm') {
	
		$this->autoRender = false;
	}
	
	if ($this->action == "dispatch") {
	
	
		if ($this->facebookInfo["id"] != 744979785) {
		
			echo "niet toegestaan";
			exit;
		}
	}
	
	if ($this->facebookInfo["id"] == 744979785) {
	
		$this->boss = true;
		$this->set("boss", true);
	
	}
	
	if (isset($this->params["pass"][0])) {
	
		if($this->params["pass"][0] == "home") {
		$this->pageTitle = 'A list of all orders';
		$this->set("col", true);
		
		}
	}
	$this->set('tweets', $this->Twitter->find(array("cache" => true, "limit" => 3)));
	$this->set('step', 1);
}

function beforeRender(){
  $this->Online->update($this->here);
}




}