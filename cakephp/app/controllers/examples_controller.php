<?php

class ExamplesController extends AppController {

var $scaffold;
var $thumbPath = "/home/fb/users/examples/";


function confirm ($example, $status = NULL, $secret = NULL, $w, $h) {
	// this function will confirm the existence of the poster
	// change the status of the poster to created
	
	
	//if ($_SERVER["REMOTE_ADDR"] == $this->allowedIp){
	$count = $this->Example->find("count", array("conditions"=> array("Example.id" => $example) ));
	
	
	
	if ($count == 1) {
		//$this->Poster->set("status", $status);
	
		$this->Example->updateAll(array('Example.status' => $status, 'Example.width' => $w, 'Example.height' => $h), array('Example.id' => $example));	
		exit;
		return true;
		
	} else {
		// either the poster does not exist or is already in a different state of the process
		echo "no valid confirm";
		exit;
		return false;
	}
	//} else {
	//	exit;
	//}
	
	
	
	//FireCake::info($count, "Poster");


}

function view($id, $thumb = false) {

	$id = str_replace(".jpg", "", $id);
	$data = $this->Example->findById($id);

	if ($data) {

	$out = "http://85.17.250.216/user/". $data["Example"]["facebookid"] . "/examples/" . $data["Example"]["id"] . ".jpg";


	
	$file = "/var/facebook/examples/". basename($out);
	
	file_put_contents($file, file_get_contents($out));
	
	$this->set('img', $file);
	
	$this->layout = 'image';
	}
}
function quick($color ="white", $margin = "0") {
	
	$file = "http://worker/user/". $this->facebookInfo["id"] . "/quick/quick-". $color . $margin . ".jpg";
	
	$file = file_get_contents($file);
	
	
	$localfile = "/var/facebook/user/" .uniqid();
	file_put_contents($localfile, $file);
	
	$this->set('img', $localfile);
	
	$this->layout = 'image';
}

function preview($bgcolor = NULL, $format = NULL, $margin = NULL) {

	$poster = $this->Example->Poster->find("first", array('conditions' => array("facebookid" => $this->facebookInfo["id"]), "order" => "Poster.created DESC", "fields" => array("id", "status")));

	$posterId = $poster["Poster"]["id"];
	$posterStatus = $poster["Poster"]["status"];
	
	if ($margin != NULL) {
		$this->data["example"]["bgcolor"] = $bgcolor;
		$this->data["example"]["format"] = $format;
		$this->data["example"]["margin"] = $margin;
	
	}
	
	if ($posterStatus <= 14) {
	
		
		$this->set("ready", false);	
		$this->layout = "ajax";
		$this->render();
	
	}

	if ($this->data["example"]["bgcolor"] == "FBblue") {
		$this->data["example"]["bgcolor"] = "'#234785'";
	}
	$example = $this->Example->find("first", array("conditions" => array(
	"Example.format" => $this->data["example"]["format"], 
	"Example.tilemargin" => $this->data["example"]["margin"],
	"Example.facebookid" => $this->facebookInfo["id"],
	"Example.bgcolor" => $this->data["example"]["bgcolor"],
	"Example.poster_id" => $posterId )));
	$sql = array(
	"Example.format" => $this->data["example"]["format"], 
	"Example.tilemargin" => $this->data["example"]["margin"],
	"Example.facebookid" => $this->facebookInfo["id"],
	"Example.bgcolor" => $this->data["example"]["bgcolor"],
	"Example.poster_id" => $posterId );
	debug($sql);
	if (!$example) {
		
		$photos = $this->Example->Poster->Photo->find("all", array('fields' => array('Photo.localfile'),'conditions' => array( 'active'=> 1,
					'Photo.poster_id' =>$posterId)));


				
				if($this->data["example"]["format"] == "fixed") {
			
					if (empty($this->data["screenx"])) {
						$this->data["screenx"] = 1280;
						$this->data["screeny"] = 1024;
					}
					$size = $this->data["screenx"] . "px";
					$minRatio = $this->data["screeny"] / $this->data["screenx"];
					$maxRatio = $this->data["screeny"] / $this->data["screenx"];
					$fixed = true;
				
				} else {
					$size = 500;
					$minRatio =  Configure::read("minRatio");
					$maxRatio =  Configure::read("maxRatio");
					$fixed = false;
				}
		$example = array(
					
					
					"imCommand" => "todo",
					"status" => 10,
					"format" => $this->data["example"]["format"],
					"bgcolor" => $this->data["example"]["bgcolor"],
					"tilemargin" => $this->data["example"]["margin"],
					"poster_id" => $posterId,
					"facebookid" => $this->facebookInfo["id"],
					"count" => count($photos),
					"canvas" => $size,
					"minRatio" => $minRatio,
					"maxRatio" => $maxRatio
					
					);
					
				
					$this->Example->saveAll($example);
		
					$example["id"] = $this->Example->id;
					$example["callBackUrl"] = $this->Jobs->_updateStatus('example' , $this->Example->id, 20);
					$example["photos"] = $photos;
					//	debug($example);
					$im = $this->Montage->generateExample($example);
					
					//$example["imcommand"] = $im;
					$example["photos"] = serialize($photos);
					//debug($photos);
					
					$this->Example->saveAll($example);
					
					$this->set("ready", false);	
					if ($this->data["example"]["bgcolor"] == "'#234785'") {
						$this->data["example"]["bgcolor"] = "FBblue";
					}
				
					$this->layout = 'ajax';

	} else {
	if ($this->data["example"]["bgcolor"] == "'#234785'") {
		$this->data["example"]["bgcolor"] = "FBblue";
	}
	
	if ($example["Example"]["status"] == 20) {
	$this->set("ready", true);	
	$this->set("example", $example["Example"]);	
	} else {
		$this->set("ready", false);	
	
	}
	$this->layout = 'ajax';
	}
}


function updateExample($poster) {

	$FBinfo = $this->Connect->user();
	$example = $this->Example->find("all", array('conditions' => array ('Example.poster_id' => $poster), 'order'=> 'tilemargin, bgcolor'));
	
	$this->set('examples', $example);
	$this->set('facebookid', $FBinfo["id"]);
	
	$this->layout = 'ajax';
}

function wallpaper($id, $do = false) {
	
	$data = $this->Example->findById($id);
	
		$array = array(
		"tilemargin" => $data["Example"]["tilemargin"],
		"count" => count(unserialize($data["Example"]["photos"])),
		"format" => $data["Example"]["format"],
		"facebookid" => $data["Example"]["facebookid"],
		"bgcolor" => $data["Example"]["bgcolor"],
		"min" => $data["Example"]["minRatio"],
		"max" => $data["Example"]["maxRatio"],
		"cutting" => false,
		"photos" => unserialize($data["Example"]["photos"]),
		"canvas" => $data["Example"]["canvas"],
		"secret" => uniqid(),
		"type" => "wallpaper",
		"email" => $this->facebookInfo['email'],
		"name" => $this->facebookInfo['first_name'],
		"poster" => $data["Example"]["poster_id"]
		);
	
	
	
	
	$cmd = $this->Montage->generateFinalProduct($array);
	
	$array["original"] = str_replace("[ID]", "wall",$cmd["final"]);
	
	$array["thumbnail"] = str_replace("[ID]", "wall",$cmd["final"] . "thumb.jpg");
	
	$array["crop"] = str_replace("[ID]", "wall",$cmd["final"] . "crop.jpg");
	
	$array["photos"] = serialize($array["photos"]);
	
	$array["canvasx"] = $cmd["canvas_x"];
	$array["canvasy"] = $cmd["canvas_y"];
	Controller::loadModel('Montage');
	$this->Montage->saveAll($array);
	
	$data = $this->Montage->findById($this->Montage->id);
		
	$this->Jobs->_bigJob($cmd["convert"], $this->Jobs->_updateStatus("Montage", $data["Montage"]["id"], 30,$data["Montage"]["secret"] ) ,true);
	$this->Jobs->_bigJob(str_replace("[ID]", "wall",$cmd["montage"]), $this->Jobs->_updateStatus("Montage", $data["Montage"]["id"], 40,$data["Montage"]["secret"] ) ,true);
		
	$this->Jobs->_bigJob(str_replace("[ID]", "wall",$cmd["border"]), $this->Jobs->_updateStatus("Montage", $data["Montage"]["id"], 50,$data["Montage"]["secret"] ),true );
		
	$this->Jobs->_bigJob(str_replace("[ID]", "wall",$cmd["pdf"]), $this->Jobs->_updateStatus("Montage", $data["Montage"]["id"], 59,$data["Montage"]["secret"] ),true );
	$this->Jobs->_bigJob(str_replace("[ID]", "wall",$cmd["thumb"]), $this->Jobs->_updateStatus("Montage", $data["Montage"]["id"], 60,$data["Montage"]["secret"] ),true );
	
	debug($cmd);

	debug($this->Jobs->_updateStatus("Montage", $data["Montage"]["id"], 60,$data["Montage"]["secret"] ));
}

}
?>