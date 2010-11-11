<?php

class MontagesController extends AppController {

var $scaffold;
var $thumbPath = "/home/fb/users/examples/";


function confirm ($example, $status = NULL, $secret = NULL) {
	// this function will confirm the existence of the poster
	// change the status of the poster to created
	
	
	//if ($_SERVER["REMOTE_ADDR"] == $this->allowedIp){
	$count = $this->Montage->find("first", array("conditions"=> array("Montage.id" => $example) ));
	

	
	if ($count) {
		//$this->Poster->set("status", $status);
		if ($status >= 60 AND $count["Montage"]["type"] != "wallpaper"){
		$this->Montage->updateAll(array('Montage.status' => $status, "Order.status" => $status), array('Montage.id' => $example));
		} else {
		$this->Montage->updateAll(array('Montage.status' => $status), array('Montage.id' => $example));
		}
		
		if($count["Montage"]["type"] == "wallpaper" AND $status == 60) {
		
		$this->Email->to = $count["Montage"]["email"];
   		$this->Email->bcc = array('dennis.vandervliet@gmail.com');  
    	$this->Email->subject = 'Vriendenprinter.nl je wallpaper is klaar';
    	$this->Email->replyTo = 'dennis@vriendenprinter.nl';
    	$this->Email->from = 'Vriendenprinter <dennis@vriendenprinter.nl>';
    	$this->Email->template = 'wallpaper'; // note no '.ctp'
    	//Send as 'html', 'text' or 'both' (default is 'text')
    	$this->Email->sendAs = 'text';
		
		$this->set("montage", $count);
		
		$this->Email->send();
			
		}
		
		
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
function pdf($id, $thumb = 1){


	$data = $this->Montage->findById($id);
	if ($data) {
	
	if ($thumb == 1) {
		$out["file"] = "http://worker/final/". basename($data["Montage"]["thumbnail"]);
	} else {
		$out["file"] = "http://worker/final/". basename($data["Montage"]["original"]);
	}
	//debug($out);
	
	$file = "/var/facebook/final/". basename($out["file"]);
	
	file_put_contents($file, file_get_contents($out["file"]));
	
	$this->set('img', $file);
	
	$this->layout = 'image';
	}

}

function view($id, $thumb = 1){


	$data = $this->Montage->findById($id);
	if ($data) {

	if ($thumb == 1) {
		$out["file"] = "http://worker/final/". basename($data["Montage"]["thumbnail"]);
	} else {
		$out["file"] = "http://worker/final/". basename($data["Montage"]["original"]);
		
	}
	//debug($out);
	if ($thumb == 3) {
	
		$this->set("noDownload", true);
	}
	$file = "/var/facebook/final/". basename($out["file"]);
	
	file_put_contents($file, file_get_contents($out["file"]));
	
	$this->set('img', $file);
	
	$this->layout = 'image';
	}

}

function dispatch($id) {
	App::import('Helper', 'Time');
	$Time = new TimeHelper();
	//build in control for more then on dispatch per order
	$this->Montage->updateAll(array('Montage.status' => 61), array('Montage.id' => $id));
	
	$montage = $this->Montage->find("first", array("conditions" => array("Montage.id" => $id)));

	if($montage["Montage"]["format"] = "landscape") {
		$x = $montage["Montage"]["canvasy"];
		$y = $montage["Montage"]["canvasx"];
	
	} else {
		$x = $montage["Montage"]["canvasx"];
		$y = $montage["Montage"]["canvasy"];
	
	}
	
	$y = round( ($y / $x) * 500, 0) . "mm";
	$x = "500mm";
	$order["naam"] = $montage["Order"]["firstname"] . " " .  $montage["Order"]["lastname"];
$order["adres_1"] = $montage["Order"]["company"];
$order["adres_2"] = $montage["Order"]["street"] . " " .  $montage["Order"]["streetnr"] . " " .  $montage["Order"]["streetnrext"];
$order["adres_3"] = $montage["Order"]["zipcode"] . " " .  $montage["Order"]["city"];
$order["adres_4"] = "NEDERLAND";
$order["email"] = $montage["Order"]["email"];
$order["produkt"] = "VriendenPrinter.nl Facebook poster";
$order["aantal"] = 1;
$order["afmetingen"] = $x ."x" .  $y;
$order["datum"] = $Time->nice($montage["Order"]["modified"]);
$order["nummer"] = $montage["Order"]["id"];
$order["crop"] = $montage["Montage"]["crop"];



	
	
	
		
	$orderAtt = "ORDER INFO";
	

	$data = array(
			"order" => $order,
			"crop" => $montage["Montage"]["crop"],
	
	
			"files" => array(
			0 => array("dest" => "dvdv-poster-" . $montage["Order"]["id"] . ".pdf", "source" => $montage["Montage"]["original"] ),
			1 => array("dest" => "dvdv-orderinfo-". $montage["Order"]["id"] . ".pdf" , "source" => "/var/facebook/orders/" . "dvdv". $montage["Order"]["id"] . ".pdf"  )
			));

	$this->Jobs->_ftpJob($data, $this->Jobs->_updateStatus("montage", $id, 70, $montage["Montage"]["secret"]));
	
	$this->Session->setFlash("De bestelling is vertuurd naar de afdrukcentrale", 'notice');
	$this->redirect("/orders/view/");



}

function detail($id = NULL, $secret = NULL) {
	if ($this->data) {
	
		
		$order_id = 	$montage = $this->Montage->find("first", array("conditions" => array("Montage.id" => $this->data["Montage"]["id"], "Montage.secret" => $this->data["Montage"]["secret"])));
		
		$this->data["Order"]["id"] = $order_id["Order"]["id"];
		$this->data["Order"]["status"] = 80;
		$this->data["Montage"]["status"] = 80;
	
		if($this->Montage->saveAll($this->data)) {
		
			$this->Session->setFlash("Bedankt! De informatie is opgeslagen", 'notice');
			
				$order_id = 	$montage = $this->Montage->find("first", array("conditions" => array("Montage.id" => $this->data["Montage"]["id"], "Montage.secret" => $this->data["Montage"]["secret"])));
				$this->Email->to = $order_id["Order"]["email"];
   				$this->Email->bcc = array('dennis.vandervliet@gmail.com');  
    			$this->Email->subject = 'Vriendenprinter.nl: bevestiging verzending bestelling nr ' . $order_id["Order"]["id"];
    			$this->Email->replyTo = 'klantenservice@vriendenprinter.nl';
    			$this->Email->from = 'Vriendenprinter <klantenservice@vriendenprinter.nl>';
    			$this->Email->template = 'tracktrace'; // note no '.ctp'
    			//Send as 'html', 'text' or 'both' (default is 'text')
    			$this->Email->sendAs = 'text'; // because we like to send pretty mail
    			//Set view variables as normal
			
				$this->set("name", $order_id["Order"]["firstname"] . " " . $order_id["Order"]["lastname"]);
				$this->set("ordernumber", $order_id["Order"]["id"]);
				$this->set("link", "https://tracktrace.tntpostpakketservice.nl/Search/Searchbasic.aspx?B=". $order_id["Order"]["track_trace"]. "&P=". $order_id["Order"]["zipcode"]);
				
				$this->Email->send();
					
		} else {
			$this->Session->setFlash("Er ging iets niet goed met het opslaan", 'notice');
		
		}
		
		$id =  $this->data["Montage"]["id"];
		$secret = $this->data["Montage"]["secret"]; 
			
	}
	
		$montage = $this->Montage->find("first", array("conditions" => array("Montage.id" => $id, "Montage.secret" => $secret)));
	
	if ($montage) {
			$this->set("montage", $montage);	
	
	} else {
	
	}


}


function sendout($id) {


				$this->Montage->updateAll(array('Montage.status' => 61), array('Montage.id' => $id));
				$montage = $this->Montage->findById($id);

				$this->Email->to = "dennis.vandervliet@gmail.com";
   				$this->Email->bcc = array("dennis.vandervliet@gmail.com");  
    			$this->Email->subject = "Nieuwe bestelling DvdV";
    			$this->Email->replyTo = 'klantenservice@vriendenprinter.nl';
    			$this->Email->from = 'Vriendenprinter <klantenservice@vriendenprinter.nl>';
    			$this->Email->template = 'send'; // note no '.ctp'
    			//Send as 'html', 'text' or 'both' (default is 'text')
    			$this->Email->sendAs = 'text'; 
    			
    			
    			$this->set("link", "http://www.vriendenprinter.nl/montages/detail/" . $montage["Montage"]["id"] . "/" . $montage["Montage"]["secret"]);
    			
    			
    			$this->Email->send();

}



function wallpaper ($id) {


	$montage = $this->Montage->findById($id);
	
	debug($montage);
	
	$this->set("montage", $montage);
}



}
?>