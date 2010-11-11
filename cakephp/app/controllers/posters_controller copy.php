<?php

class PostersController extends AppController {

var $scaffold;
var $sh;
var $imgDir = "/var/facebook/";
var $wget;
var $imglist;
var $confirmUrl = "http://www.manjano.nl/posters/confirm/";
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
var $wwwBase = "http://www.manjano.nl/";
var $posterBgColor = "white";
var $storage = "/var/facebook/";
var $q;
var $t;
var $exampleThumbSize = 200;

var $helpers = array( 'Colorbox');

function _allocateTube ($type = 'gen') {
	if ($this->q) {
		return $this->t;
	} else {
		$this->q = ClassRegistry::init('Queue.Job');
		$this->t = $this->q->listTubes($type);
		if (empty($this->t)) {
			$this->t = $type . "-" . uniqid();
			return $this->t;
		}
		return $this->t;	
	}

}
function _copyExample($facebookid, $callback = NULL, $id) {
	echo $id;
	$payload = array(
			'facebookId' => $facebookid,
			'type' => 'example_copy',
			'callBackUrlSuccess' => $callback,
			'path' => '/home/fb/users/examples/',
			'id' => $id);
			
			$options = array('tube' => $this->_allocateTube('img'));
			
		//$this->q = ClassRegistry::init('Queue.Job');
		$this->q->put($payload, $options); 
}

function _imageJob($photo, $facebookid, $callback = NULL) {

		$payload = array(
			'array' => $photo,
			'facebookId' => $facebookid,
			'type' => 'image',
			'callBackUrlSuccess' => $callback);
			
		$options = array('tube' => $this->_allocateTube('img'));
			
		//$this->q = ClassRegistry::init('Queue.Job');
		$this->q->put($payload, $options); 
		
		
		//FireCake::info($options, "jobinfo");
		

}

function _directoryJob($photo, $facebookid, $example, $callback = NULL) {
	//echo $facebookid;
	$payload = array(
			'array' => $photo,
			'facebookId' => $facebookid,
			'type' => 'dir',
			'callBackUrlSuccess' => $callback);
			
		$options = array('tube' => $this->_allocateTube('img'));
			
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
	if ($do) {
		$this->confirm($id, $status, $secret, true);
		
	} else {
	
		return $callback;
	}
}	

function _queueJob($cmd, $callback = NULL, $facebookid = NULL) {
		
	

	
	if ($cmd == '') {
	} else {
		$payload = array(
	 'callBackUrlSuccess' => $callback,
	 'command' => $cmd,
	 'facebookId' => $facebookid,
	 'type' => 'example',
	 'exampleId' => $this->Poster->Example->id);
	
		$options = array('tube' => $this->_allocateTube('gen'));
			
		//$this->q = ClassRegistry::init('Queue.Job');
		$this->q->put($payload, $options); 
		
		//FireCake::info($callback, "cmd");
		//FireCake::info($options, "options");
	}
}



function _imglist ($url = NULL, $poster = NULL, $options = array()) {
	if ($url == NULL) {
		return $this->imglist;
	} else {
		$filename = basename($url);
		if (file_exists($this->imgDir . "/base/". $filename)) {
			$base = $this->imgDir . "base/". $filename;
			$target = $this->imgDir . $poster. "/originals/". $filename;
			//echo $base;
			//echo $target;

			if (file_exists($target)){
			//echo "NO COPY";
			} else {
			symlink($base, $target );
			}
		} else {
		$this->imglist.= $url . "\r\n";
		}
	}
}


function _cropresize($facebookid, $example = true, $vertical = true, $poster) {
	
	if ($example == true) {
		$height = $this->exampleRatio * $this->posterHeight;
		$width = $this->exampleRatio * $this->posterWidth;
		$margin = ceil($this->exampleRatio * $this->posterTileMargin);
		$dir = "";
		
	} else {
		$height = $this->posterHeight;
		$width = $this->posterWidth;
		$margin = ceil($this->posterTileMargin);
		$dir = "big/";
	}
	

	
	$countX = ceil(sqrt(($this->count)/2));
	$countY = ceil($this->count/$countX);
	
	if ($vertical == true) {
		$tileSize = floor((($height - ($margin*($countY+1)))/$countY)); 
	
	} else {
		$tileSize= floor((($width - ($margin*($countX+1)))/$countX));

	}
	
	
		
		
	if ($example == true) {
		
		$output = $this->imgDir .'user/'. $facebookid . "/thumbnails/" . $tileSize .  "-poster-" . $poster . "%d.thumb";
		
		$this->imQuality = " -quality 70 ";
		
	} else {
		$output = $this->imgDir . 'user/'.$facebookid . "/bigthumbnails/" . $tileSize . "-poster-" . $poster . "%d.thumb";
		
		

	}
	


	
	
	$cmd = "/usr/bin/convert ". $this->imLimit . $this->imgDir . 'user/'. $facebookid . "/originals/* -resize x".$tileSize. $this->imQuality . " -resize '".$tileSize."<'". $this->imQuality . " -gravity center -crop ".$tileSize."x".$tileSize."+ -gravity southeast -background blue -splice 20x10". " +repage " . $output. "\n";
	
	//FireCake::info($cmd, "_cropresize command");
	return $cmd;
	
	

}

function _montage($facebookid, $example = true, $vertical = true, $poster, $options = NULL) {
	if (is_array($options)) {
		foreach (array_keys($options) as $option) {
			switch($option)	{
				case "tilemargin":
					$this->posterTileMargin = $options[$option];		
					break;
				case "bgcolor":
					$this->posterBgColor = $options[$option];
					break; 
			
			}	
		}
	}
	
	
	if ($example == true) {
		$ratio = $this->exampleRatio;
		$dir = $this->imgDir .'user/'. $facebookid . "/thumbnails/* ";
	} else {
		$ratio = 1;
		$dir = $this->imgDir . 'user/'. $facebookid . "/bigthumbnails/* ";
	}

	if ($vertical == true) {
		$height = $ratio * $this->posterHeight;
		$width = $ratio * $this->posterWidth;
		$margin = $this->posterTileMargin;
		$countX = ceil(sqrt(($this->count)/2));
		$countY = ceil($this->count/$countX);
		$name = "-vertical";

		//$d= array($vertical, $height, $width, $margin, $countX, $countY, $tileSizeH, $tileSizeW);

			
		$tileSizeH = floor($height / (($countX) + (($countX + 1) * $margin))); 

	
		$tileSizeW = floor($width / (($countY) + (($countY + 1) * $margin)));
	
	} else {
		$width = $ratio * $this->posterHeight;
		$height = $ratio * $this->posterWidth;
		$margin = $this->posterTileMargin;
		$countY = ceil(sqrt(($this->count)/2));
		$countX = ceil($this->count/$countY);
		$name = "-horizontal";
	
		$tileSizeH = floor($width / (($countX) + (($countX + 1) * $margin))); 

	
		$tileSizeW = floor($height / (($countY) + (($countY + 1) * $margin)));
	
		//$d= array($vertical, $height, $width, $margin, $countX, $countY, $tileSizeH, $tileSizeW, $tileSize);
		
		
	}
	
	
	//$tile = floor($width / (($countX) + (($countX + 1) * $margin)));
	$tileSizeH = floor($width / (($countX) + (($countX + 1) * $margin))); 

	
	$tileSizeW = floor($height / (($countY) + (($countY + 1) * $margin)));
	
	//echo $tileSizeH . "---". $tileSizeW;
	if ($tileSizeH > $tileSizeW) {
		$tileSize = $tileSizeW;
		
	} else if ($tileSizeH < $tileSizeW) {
		$tileSize = $tileSizeH;
	
	} else {
	
		$tileSize = $tileSizeH;
	}
	
	



	//$margin = floor($margin * $tileSize);
	
	if ($example == true) {
		
		
		$output = $this->imgDir . 'user/'. $facebookid . "/examples/" . $tileSize . "-" .$countX . "-" .$countY . "-poster-" . $poster . $name. "-". uniqid() . ".jpg";
	
			$margin = ceil($margin * 50);
		
			// correct for the fact that we use the pre generated thumbnails
		if($this->Poster->Example->find("first", array("conditions" => array("file" => $output)))) {
			return false;
		
		
		} else {
			$dirT = $this->imgDir . 'user/' . $facebookid;
		
			$realTileSize = $tileSize / $this->exampleRatio;
			$realMargin = $margin / $this->exampleRatio;
			$realGeo = " -geometry " . $realTileSize . "x". $realTileSize . " ";
			
			$realCmd = "rm ". $dirT. "/bigthumbnails/*\n";
		
		$realCmd .= "/usr/local/bin/convert ". $dirT . "/originals/* -resize ". $realTileSize . " -quality 100 -resize 'x". $realTileSize . "<' -quality 100 -gravity center  -crop ".$realTileSize. "x". $realTileSize . "+0+0 +repage -quality 100 -gravity southeast -background ". $this->posterBgColor ." -splice ".$realMargin . "x" . $realMargin." ". $dirT . "/bigthumbnails/%d.thumb.jpg\n";
		
		
			
			$tile  = "-tile ".$countX."x". $countY . " ";	
			
			$realCmd .= "/usr/local/bin/montage -background " . $this->posterBgColor . " " .  $dirT."/bigthumbnails/* ". $tile . $realGeo .   $dirT . "/final.jpg\n";
			
			
			$realCmd .= "/usr/local/bin/convert ". $dirT . "/final.jpg -bordercolor " . $this->posterBgColor . " -border ". $realMargin . "x" . $realMargin . " "  . $dirT . "/final.jpg";
			
			FireCake::info($realCmd, "cmd");
	
			//$realCmd .= $this->_montage($facebookid, false, $vertical, $poster, $options);
		
			$data = array("Example" => array("imcommand" => $realCmd, "status" => 10, "poster_id" => $poster, 'vertical' => $vertical, 'bgcolor' => $this->posterBgColor, 'tilemargin' => $this->posterTileMargin, 'thumbnail' =>  $output . '.thumb.jpg', 'facebookid' => $facebookid ));
			
		
			$this->Poster->Example->saveAll($data);
			
			$geometry = "-geometry 50x50+".$margin. "+". $margin . " ";
			
		$output = $this->imgDir . 'user/'. $facebookid . "/examples/" . $this->Poster->Example->id. ".jpg";
	
		}
				
	} else {
	
		
	
		$output = $this->imgDir . 'user/'. $facebookid . "/" . $tileSize . "-" .$countX . "-" .$countY . "-poster-" . $poster . $name . ".jpg";
		
			$geometry = "-geometry ".$tileSize ."x".$tileSize. "+".$margin. "+". $margin . " ";

	}
	
		$d= array($vertical, $height, $width, $margin, $countX, $countY, $tileSizeH, $tileSizeW, $tileSize, 'sizeX' => ($countX * $tileSize)+ (($countX+1) * $margin) , $output);
	
		FireCake::info($d, 'debug');
	//$geometry = "-geometry ".$tileSize ."x".$tileSize. "+".$margin. "+". $margin . " ";
	
	FireCake::info($geometry, "geo");
	$tile  = " -tile ".$countX."x". $countY . " ";	
	
	//$geometry = "-geometry ".$tileSize ."x".$tileSize. "+".$margin. "+". $margin . " ";
	
	$cmd = "/usr/local/bin/montage".$this->imLimitMontage. $this->imQuality . " -background " . $this->posterBgColor . " " .  $dir. $tile . $geometry. $output. "\n";
	
	if ($example == true) {
		
		$per = floor(($tileSize/50) * 100);
		$cmd .= "/usr/local/bin/convert -resize ".$per."% " . $output . " " .$output . "\n";

	
	}
	
	$cmd .= "/usr/local/bin/convert -crop 200x200+0x0 +repage " . $output . " " .$output . ".thumb.jpg\n";
	
	FireCake::info($cmd , "cmd");

	return $cmd;
	
	}

function _recursiveDelete($str){

        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                $this->_recursiveDelete($path);
            }
            return @rmdir($str);
        }
 }

function _createDirs ($poster){
	if (!file_exists($this->imgDir . $poster)) {
		!mkdir($this->imgDir . $poster);
		!mkdir($this->imgDir . $poster."/originals" );
		!mkdir($this->imgDir . $poster."/thumbnails");
		//!mkdir($this->imgDir . $poster."/thumbnails/big");
		!mkdir($this->imgDir . $poster."/examples");
		!mkdir($this->finalDir . $poster);
	} else {
		
		$this->_recursiveDelete($this->imgDir. $poster . "/originals/");
		$this->_recursiveDelete($this->imgDir. $poster . "/thumbnails/");
		$this->_recursiveDelete($this->imgDir. $poster . "/examples/");
		!mkdir($this->imgDir . $poster."/examples");
		!mkdir($this->imgDir . $poster."/originals" );
		!mkdir($this->imgDir . $poster."/thumbnails");

	
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
		$this->Session->setFlash("We gaan nu al je vrienden zoeken op facebook", "search");
		
		
		$this->redirect("/posters/create");	
		
	} else {
		
		
		$this->set("step", 1);
		
	}

	
}

function create() {
	
	
	if ($this->Connect->user()) {
	
	$FacebookApi = new FB();
	
	
	$FacebookFriends = $FacebookApi->api('/me/friends');
	
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
	
	$fql = "SELECT src_big, src_big_width, src_big_height, owner FROM photo WHERE pid IN (SELECT cover_pid, owner, name FROM album WHERE (".$condition .") AND (name = 'Profile Pictures' OR name = 'Profile pictures'))";
	
		
	$big = Cache::read(md5($fql));
	
	if($big == false ){
		$big = $FacebookApi->api($this->_fql($fql));	
		Cache::write(md5($fql), $big);
	} 
		
	//Cache::write('test', 'hallo', 36000);
	//$serial = serialize($big);
	
	//file_put_contents('/home/fb/cakephp/fql.txt', $serial);
	
	//print_r($big);
	
	//$big = unserialize(file_get_contents('/home/fb/cakephp/fql.txt'));

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
	
		//FireCake::info($small, 'real');
	//$serial = serialize($small);
	
	//file_put_contents('/home/fb/cakephp/fqlsmall.txt', $serial);
	
	//$small = unserialize(file_get_contents('/home/fb/cakephp/fqlsmall.txt'));
	
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
	
		print_r($poster);
		
	$callback = $this->_updateStatus('poster', $this->Poster->id, 15,$secret );

	$this->_imageJob($photo, $FacebookId['id'], $callback); //will process all images and start downloading them
	
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
			Cache::write(md5($fql), $big);
		} 
	
		$square = $FacebookApi->api($this->_fql($fql));	
		//usort($square, "cmp");
		//FireCake::info($photo, "fql");
		
		$this->set("friends", $square);
		
		$this->set("step", 2);
		//$this->build($this->Poster->id);
	} else {
	
		// redirect to loginfb
	}		

}


function build ($poster = NULL) {
Controller::loadModel('Photo');

	if (!$this->data){
		$this->redirect('/posters/create');
	
	}
	

	
	
	$FBinfo = $this->Connect->user();
	//FireCake::info($this->Connect->user(), "FBuser");
	
	//print_r($FBinfo);
	
	$poster = $this->Poster->find("first", array(
											'order' => 'created DESC',
											'conditions' => array( 'facebookid'=> $FBinfo["id"])));

	$callback = $this->_updateStatus('poster', $poster['Poster']['id'], 16,$poster['Poster']['secret'], true );
	
		
	$condition = array();
	$z = 0;
	foreach (array_keys($this->data["friends"]) as $friendid) {
		$condition[$z] = $friendid;
		
		$z++;
			
	}	
	
	$friend = $this->Photo->find("all", array ('fields' => array('id', 'facebookid'),
												'conditions' => array('poster_id' => $poster['Poster']['id'], 'active' => 1)));
	//print_r($friend);											
							
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
												
	
	//$query = "UPDATE photos SET active=0 WHERE poster_id=". $poster["Poster"]["id"];
	
	//$this->Poster->query($query);
	$c = $this->Photo->updateAll(array('active' => 0), array('Photo.poster_id' => $poster["Poster"]["id"], 'Photo.facebookid' => $notactive ));
	
	//print_r($c);
	$this->Photo->updateAll(array('active' => 1), array('Photo.facebookid' => $condition, 'Photo.poster_id' => $poster["Poster"]["id"] ));
	
	$photos = $this->Poster->Photo->find("all", array('fields' => array('Photo.localfile'),
											'conditions' => array( 'active'=> 1,
																	'Photo.poster_id' =>$poster["Poster"]["id"])));
	
	
									
	foreach($photos as $photo) {
		$this->count++;
	}
	
	
	$callback = $this->_updateStatus('poster', $poster['Poster']['id'], 20,$poster['Poster']['secret'] );
	
	$this->_directoryJob($photos, $FBinfo['id'], true, $callback);
	
	$this->Poster->Example->deleteAll(array('Example.poster_id'=> $poster["Poster"]["id"]));
	
	$bgcolors = array("black", "white", "'#234785'");
	
	$margins = array(0, "0.03", "0.07");

	foreach ($bgcolors as $bgcolor) {
	
		foreach ($margins as $margin) {
		//echo $this->_montage($FBinfo['id'], true, false, $poster["Poster"]["id"], array('tilemargin' => $margin, 'bgcolor'=> $bgcolor));
		
		$this->_queueJob($this->_montage($FBinfo['id'], true, true, $poster["Poster"]["id"], array('tilemargin' => $margin, 'bgcolor'=> $bgcolor)), $this->_updateStatus('example' , $this->Poster->Example->id, 20), $FBinfo['id'] );
		
		//echo $this->_montage($FBinfo['id'], true, false, $poster["Poster"]["id"], array('tilemargin' => $margin, 'bgcolor'=> $bgcolor));
		
		
		//$this->_copyExample($FBinfo['id'], $this->_updateStatus('example' , $this->Poster->Example->id, 20), $this->Poster->Example->id );
				
		}
	
	}
	
	$this->set('step',	2);
	$this->set('test', 'begin');
	$this->set('data', $poster);
	//$this->flash("Voorbeelden worden gemaakt, even geduld aub", array("controller" =>  "poster", "action" => "view"));
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
	$payload = array(
			'type' => 'test');
			
		$options = array('tube' => $this->_allocateTube('img'));
	$this->q->put($payload, $options);
	print_r($options);
	$this->autoRender = false;
}
function updateStatus($id) {

	$this->set('test', uniqid());
	
	$this->layout = 'ajax';


}


function updateExample($poster) {

	
	$example = $this->Poster->Example->find("all", array('conditions' => array ('Example.poster_id' => $poster)));
	
	$this->set('examples', $example);
	
	FireCake::info($example, 'example');	
	$this->layout = 'ajax';
}
}
?>