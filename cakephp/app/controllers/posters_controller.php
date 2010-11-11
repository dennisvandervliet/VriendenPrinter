<?php

class PostersController extends AppController {

var $scaffold;
var $sh;
var $imgDir = "/var/facebook/";
var $wget;
var $imglist;
var $confirmUrl = "http://www.vriendenprinter.nl/posters/confirm/";
var $allowedIp = "95.211.18.160";
var $imgDirWeb = "/img/";
var $blankImg = '/home/fb/cakephp/app/webroot/img/no.jpg';
var $queueDir = '/home/fb/cakephp/tmp/q/';
var $finalDir = '/home/fb/cakephp/final/';
var $count = 0;
var $imgDirT = "/home/fb/cakephp/app/webroot/img/";
var $posterHeight = 6000;
var $posterWidth = 3000;
var $exampleRatio = 0.1;
var $posterTileMargin = 2;
//var $imLimit = " -limit memory 128mb -limit map 64mb -limit area 32mb ";
//var $imLimitMontage = " -limit memory 128 -limit map 64 -limit area 32 ";
var $imLimit = "";
var $imLimitMontage = "";
var $imResizeFilter = "";
var $imQuality = " -quality 60 ";
var $wwwBase = "http://www.vriendenprinter.nl/";
var $posterBgColor = "white";
var $storage = "/var/facebook/";
var $q;
var $t;
var $exampleThumbSize = 200;

var $helpers = array( 'Colorbox');






function _updateStatus($model, $id, $status, $secret = NULL, $do = false) {
	if ($model == NULL) {
			$callback = NULL;
	} else {
		$callback = $this->wwwBase . $model . "s/confirm/" . $id . "/" . $status . "/" . $secret;
	}
	if ($do) {
		$this->confirm($id, $status, $secret, true);
		
	} else {
	
		return $callback;
	}
}	



function _fql($query) {

	$return = array('method'=> 'fql.query',
					'query'=> $query,
					'callback'=> '');
					
	return $return;
}

function _fileext($file) {
	return end(explode(".", $file));

}



function index($go = NULL) {

	if ($this->Connect->me) {
				
		$this->set("step", 1);
		//$this->Session->setFlash("We gaan nu al je vrienden zoeken op facebook", "search");
		
		
		$this->redirect("/posters/create");	
		
	} else {
		
		
		$this->set("step", 1);
		
	}

	$this->set('step',	1);
	$this->set("sidebar", 1);
}

function create($what = NULL) {
	if ($what == "realthing") {
			$this->Session->write("what", "realthing");
	} else {
		$this->Session->delete("what");
	}
	 

	if ($this->Connect->user()) {
	
	$FacebookApi = new FB();
	
	
	$FacebookFriends = $FacebookApi->api('/me/friends');
	if (count($FacebookFriends["data"]) == 0) {
	
		$this->Session->setFlash("Sorry we kunnen je lijst met vrienden niet ophalen", "error");
		$this->redirect("/");
	}

	//FireCake::info($FacebookFriends, "Poster Id");

	
	$this->set('FacebookFriends', $FacebookFriends['data']);
	$x = 0;
	$condition = "";	
	
	foreach ($FacebookFriends["data"] as $friend) {
		$FriendId = $friend["id"];
		$FriendList[$x] = $friend["id"];
		
		if ($condition == "") {
		
			$condition .= "owner = ". $FriendId;
		}	else {
			$condition .= " OR owner = ". $FriendId;
		
		
		}
		

		/**if ($query) {
			// now we have big picture
			$Photos[$x] = array(
				"url" => $query[0]["src_big"],
				"height" => $query[0]["src_big_height"],
				"width" => $query[0]["src_big_width"],
				"big" => true);
			
		} else {
		
			$fql = "SELECT pic_big FROM profile WHERE id=".$FriendId;
			
			$query = $FacebookApi->api($this->_fql($fql));	
			
			$Photos[$x] = array("url"=> $query[0]["pic_big"]);
		
		}
		**/
		if( $x == 50000) {
			break;
		}
		$x++;
	}
	
	$square_condition = $condition;
	
	$fql = "SELECT src_big, src_big_width, src_big_height, owner FROM photo WHERE pid IN (SELECT cover_pid, owner, name FROM album WHERE (".$condition .") AND (name = 'Profile Pictures' OR name = 'Profile pictures' OR name = 'Profielfoto\'s'))";
	//$fql = "SELECT cover_pid, owner, name FROM album WHERE (".$condition ." ) AND name = 'Profielfoto\'s' ";
		//debug($fql);
	$big = Cache::read(md5($fql));
	
	if($big == false ){
		$big = $FacebookApi->api($this->_fql($fql));
		Cache::write(md5($fql), $big);
	} 
	
	$BigPhoto = array();
	$y = 0;
	foreach ($big as $row) {
		$BigPhoto[$y] = $row["owner"];
		$y++;
	}
	
	$condition = "";
 	foreach ($FriendList as $friend) {
 	
 		if(in_array($friend, $BigPhoto)) {
 		
 			
 		} else {
 			// no high resolution profile picture get a smaller one
 			
			if ($condition == "") {
		
				$condition .= "id = ". $friend;
			}	else {
				$condition .= " OR id = ". $friend;
		
			}
 					
 		}
 	}


	$fql = "SELECT pic_big, id FROM profile WHERE (".$condition.")";
	//echo md5($fql);
	$small = Cache::read(md5($fql));
	//FireCake::info($small, 'cache');

	if($small == false ){
		$small = $FacebookApi->api($this->_fql($fql));	
		Cache::write(md5($fql), $small);
	} 
	//debug($small);
	
	$z = 0;
	foreach ($small as $s) {
		if ($this->_fileext($s["pic_big"]) != "gif"){
		$photo[$z]["url"] = $s["pic_big"];
		$photo[$z]["big"] = false;
		$photo[$z]["facebookid"] = $s["id"];
		$photo[$z]["localfile"] = md5($s["pic_big"]);
		$z++;
		}
	}	
	foreach ($big as $b) {
		if ($this->_fileext($b["src_big"]) != "gif"){
		$photo[$z]["url"] = $b["src_big"];
		$photo[$z]["big"] = true;
		$photo[$z]["height"] = $b["src_big_height"];
		$photo[$z]["width"] = $b["src_big_width"];
		$photo[$z]["facebookid"] = $b["owner"];
		$photo[$z]["localfile"] = md5($b["src_big"]);
		$z++;
		}
	
	}
		//$photo = array_slice($photo, 0, 15);	
		
		//echo count($photo);
		$FacebookId = $FacebookApi->api("/me");
		$secret = $code = md5(uniqid(rand(), true));
		$data = array("Poster"=>array('facebookid'=> $FacebookId['id'], 'status'=>10, 'secret'=>$secret),					"Photo" => $photo );
	
	
		$poster = $this->Poster->saveAll($data);
	
				
//will process all images and start downloading them
	
	$condition = "";
	foreach ($photo as $friend) {
		$FriendId = $friend["facebookid"];
		
		if ($condition == "") {
		
			$condition .= "id = ". $FriendId;
		}	else {
			$condition .= " OR id = ". $FriendId;
		
		
		}		
	}	
		$fql = "SELECT name, pic_square, id FROM profile WHERE (". $condition .")";
		
		
		$square = Cache::read(md5($fql));
	
		if($square == false ){
			$square = $FacebookApi->api($this->_fql($fql));	
			Cache::write(md5($fql), $square);
		} 
		$xx = 0;
		$new_square= array();
		foreach ($square as $s) {
			
			//debug($s["id"]);
		
			$id = $this->_recursiveArraySearch($photo, $s["id"], 'facebookid');
			
			//debug(md5($photo[$id]['url']));
			$xx++;
			$new_square[$xx] = array("id" => $s["id"], "name" => $s["name"], "pic_square" => $s["pic_square"], "md5" => md5($photo[$id]['url']));
		}
		
	
		
		//$square = $FacebookApi->api($this->_fql($fql));	
		//usort($square, "cmp");
		//FireCake::info($photo, "fql");
		shuffle($square);
		/*foreach ($square as $s) {
		
			$d = $this->_recursiveArraySearch($photo, $s["id"], "facebookid");
			echo "<img src='". $s["pic_square"] . "'><img src='". $photo[$d]['url'] . "' width=100><br>";
			
			
		}*/
		$callback = $this->_updateStatus('poster', $this->Poster->id, 15,$secret );

		$this->Jobs->_thumbnailJob($new_square, $FacebookId['id'], $callback); 
		
		
		$callback = $this->_updateStatus('poster', $this->Poster->id, 15,$secret );

		$this->Jobs->_imageJob($photo, $FacebookId['id'], $callback); 
		
		
		
		
		$this->set("friends", $square);
		$this->set("sidebar", 1);
		$this->set("step", 2);
		//$this->build($this->Poster->id);
	} else {
	
		// redirect to loginfb
	}		

}


function build ($poster_id = NULL, $try = 0) {
Controller::loadModel('Photo');
	
	

		$FBinfo = $this->Connect->user();
	if ($poster_id) {
		$poster = $this->Poster->find("first", array(
											'order' => 'created DESC',
											'conditions' => array( 'id'=> $poster_id)));
		if ($poster["Poster"]["status"] <= 14) {
			$this->redirect("/posters/evengeduld/" .$poster_id. "/". $try);
		}
		
		$photos = $this->Poster->Photo->find("all", array('fields' => array('Photo.localfile'),
											'conditions' => array( 'active'=> 1,
																	'Photo.poster_id' =>$poster_id)));
		// load poster info an check for status
	} else if ($this->data) {
	

		$poster = $this->Poster->find("first", array(
											'order' => 'created DESC',
											'conditions' => array( 'facebookid'=> $FBinfo["id"])));
				$condition = array();
		$z = 0;
		foreach (array_keys($this->data["friends"]) as $friendid) {
			$condition[$z] = $friendid;
		
			$z++;
			
		}	
	
		$friend = $this->Photo->find("all", array ('fields' => array('id', 'facebookid'),
												'conditions' => array('poster_id' => $poster['Poster']['id'], 'active' => 1)));
									
							
		$x = 0;	
		$notactive = array();										
		foreach ($friend as $f) {
			if (in_array($f["Photo"]['facebookid'], $condition) ) {
				$active[$x] = $f["Photo"]['facebookid'];
			} else {
				$notactive[$x] = $f["Photo"]['facebookid']; 
			}
		$x++;
		}											
												
	
		$c = $this->Photo->updateAll(array('active' => 0), array('Photo.poster_id' => $poster["Poster"]["id"], 'Photo.facebookid' => $notactive ));
	

		$this->Photo->updateAll(array('active' => 1), array('Photo.facebookid' => $condition, 'Photo.poster_id' => $poster["Poster"]["id"] ));
	
		$photos = $this->Poster->Photo->find("all", array('fields' => array('Photo.localfile'),
											'conditions' => array( 'active'=> 1,
																	'Photo.poster_id' =>$poster["Poster"]["id"])));
		
		
		if ($poster["Poster"]["status"] <= 14) {
			$this->redirect("/posters/evengeduld/" .$poster["Poster"]["id"]. "/". $try);
			
			
		}
		
		// save active photos
	} else {
		// empty data so go back to create
		$this->Session->setFlash("Selecteer de vrienden die je op de poster wil hebben", "notice");
		$this->redirect('/posters/create');
	
	}
	
	$callback = $this->_updateStatus('poster', $poster['Poster']['id'], 20,$poster['Poster']['secret'] );


	
	$sizes = $this->Montage->determineDimensions(count($photos), Configure::read("minRatio"), Configure::read("maxRatio"), 100, 0.1);
	
	
	
	$this->Jobs->_directoryJob($photos, $FBinfo['id'], true, $callback, $sizes);
	
	
	$this->Montage->generateQuickView($FBinfo['id'], 16);
	
	$this->Poster->Example->deleteAll(array('Example.poster_id' => $poster["Poster"]["id"], "Example.locked" => 0));
	
	debug(count($photos));
	$margins = array(0, "0.05", "0.1");
	$bgcolors = array("white", "black", "'#234785'");
	$formats = array("square", "landscape", "portrait");
	
	
		$this->set("realtime", false);

	// now we generate all the examples at once

	$this->Session->write("poster_id", $poster["Poster"]["id"]);								
	foreach($photos as $photo) {
		$this->count++;
	}
	
	$this->set("what", $this->Session->read("what"));
	
		
	$this->set('step',	3);
	$this->set("sidebar", 1);
	$this->set('test', 'begin');
	$this->set('data', $poster);

}

function confirm ($poster, $status = NULL, $secret = NULL, $do = false) {
	// this function will confirm the existence of the poster
	// change the status of the poster to created
	
	
	//if ($_SERVER["REMOTE_ADDR"] == $this->allowedIp){
	$count = $this->Poster->find("count", array("conditions"=> array("id" => $poster, "secret" => $secret) ));
	
	
	
	if ($count == 1) {
		//$this->Poster->set("status", $status);
	
		$this->Poster->updateAll(array('Poster.status' => $status), array('Poster.id' => $poster));
		return true;
	} else {
		// either the poster does not exist or is already in a different state of the process
		echo "no valid confirm";
		return false;
	}
	if($do == false) {
		exit;
	}
	//} else {
	//	exit;
	//}
	
	
	
	//FireCake::info($count, "Poster");


}

function view() {

	if ($this->Connect->user()) {
	$FBinfo = $this->Connect->user();
	
	$poster = $this->Poster->find("first", array(
											'order' => 'created DESC',
											'conditions' => array( 'facebookid'=> $FBinfo["id"])));
	//	print_r($poster);
		if($poster["Poster"]["status"] > 50) {
		
			$this->flash("Er worden voorbeelden voor je gemaakt, een moment", array("controller" => "posters", "action" => "view"));
		
		} else {
			
			$this->set("poster", $poster);
		
		}
	
	} else {
	
	}
}

function img($type = "example", $id) {

	if ($this->Connect->user()) {
		$FBinfo = $this->Connect->user();
		
		$poster = $this->Poster->Example->find("first", array(
											'order' => 'Poster.created DESC',
											'conditions' => array( 'Poster.facebookid'=> $FBinfo["id"], "Example.id" => $id)));
											
		//FireCake::info($poster, "image");	
		
		$img = $poster["Example"]["file"];								
											
		$this->set('img', $img);

		$this->layout= 'ajax';
	} else {
	
		$this->set('img', $this->blankImg);
		
		$this->layout = 'image';
	}
}



function sleep(){
	
$this->q = ClassRegistry::init('Queue.Job');

debug($this->q->lt());



}
function stress() {
	//$fql = "SELECT src FROM photo";
	$FacebookApi = new FB();
		
		$fql = "SELECT aid, cover_pid FROM album WHERE ( owner = 705207175 ) AND name = 'Profielfoto\'s' ";
		$big = $FacebookApi->api($this->_fql($fql));
	debug($big);
		$fql = "SELECT pid, src_small, created, modified FROM photo WHERE ( aid = 3028841753529833874 ) ORDER BY created DESC";
		$big = $FacebookApi->api($this->_fql($fql));
	debug($big);
	
		$fql = "SELECT pic_big, id FROM profile WHERE ( id = 705207175 )";
		$big = $FacebookApi->api($this->_fql($fql));
	debug($big);
		
		
	}
function _recursiveArraySearch($haystack, $needle, $index = null) 
{ 
    $aIt     = new RecursiveArrayIterator($haystack); 
    $it    = new RecursiveIteratorIterator($aIt); 
    
    while($it->valid()) 
    {        
        if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) { 
            return $aIt->key(); 
        } 
        
        $it->next(); 
    } 
    
    return false; 
} 
	
	
	
function updateExample($poster) {

	
	$example = $this->Poster->Example->find("all", array('conditions' => array ('Example.poster_id' => $poster)));
	
	$this->set('examples', $example);
	
	FireCake::info($example, 'example');	
	$this->layout = 'ajax';
}

function evengeduld($id, $try = NULL) {
	$this->set("try", $try);
	$this->set("id", $id);
}
}
?>