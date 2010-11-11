<?

class OrdersController extends AppController {
var $scaffold;
var $idealLib = "/home/fb/ideal/lib/";
var $issuer;
var $q;
var $t;
var $wwwBase = "http://www.manjano.nl/";


var $helpers = array("Colorbox");
function create ($id = NULL) {

	//lock example so it can not be changed or deleted
	
	if (empty($this->data) AND $id != NULL) {
	
	//show form
	
	
		Controller::loadModel('Example');
		
		
		$this->Example->updateAll(array("Example.locked" => 1), array("Example.id" => $id)); 
		$this->set("step", 4);
		$this->set("form", true);
		$this->Session->setFlash('Je naam en e-mail hebben we alvast voor je ingevuld', 'notice');
		$this->set("exampleId", $id);
		$this->set("sidebar", 1);
	
	} else {
		Controller::loadModel('Example');
		$this->set("exampleId", $this->data["Order"]['example_id']);
		
		
		$format = $this->Example->findById($this->data["Order"]['example_id']);
		
		if(!$format) {
		$this->Session->setFlash('Maak eerst een poster aan');
		$this->redirect("/posters/create");
		
		}
		
		
		if($format["Example"]["format"] == "square") {
		
			$this->data["Order"]["subtotal"] = 1795;
		} else {
			$this->data["Order"]["subtotal"] = 2195;
		
		}
		
		//$this->data["Order"]["example_id"] = $id;
		
		
		
		$this->data["Order"]["shipping"] = 0;
		
		$this->data["Order"]["total"] = $this->data["Order"]["subtotal"] + $this->data["Order"]["shipping"];
		
		$this->data["Order"]["Example"]["locked"] = 1;
		
		$this->data["Order"]["status"] = 10;
		
		$this->data["Order"]["facebookid"] = $this->facebookInfo["id"];
		
				
		if ($this->Order->save($this->data)) {
			
			
			$this->set("form", false);
			$this->set("sidebar", 1);
			
			
			//print_r($this->Order->id);
			$this->Session->write("order_id", $this->Order->id);

			$this->redirect('/orders/check/');
			
			
		} else {
			$this->set("form", true);
	
			$this->set("sidebar", 1);
			$this->set("step", 4);
		}
	
	// handle errors
	
	
	// start payment

	
	}
	
	
}

function check() {
	$id = $this->Session->read("order_id");
	if (!$id) {
		$this->Session->setFlash('De poster die je wil bestellen bestaat niet, kies een nieuwe', 'error');
		$this->redirect('/posters/create/');

	}
	if (empty($this->data)){
		$order = $this->Order->find("first", array('conditions' => array('Order.id' => $id, "Order.status <=" => 10)));
		// 
		if (!$order) {
			$this->Session->setFlash('Deze order bestaat niet', 'error');
			$this->redirect("/posters/create/");
		} 
		
		if($order["Order"]["payment"] == 1) {
			$this->Session->setFlash('Deze order is al betaald, maak een nieuwe bestelling aan.', 'error');
			$this->redirect("/posters/create/");

		
		}
		
		$facebookid = $order["Example"]["facebookid"];
		$exampleid = $order["Example"]["id"];
		
		$original = "http://www.vriendenprinter.nl/examples/view/".$exampleid. ".jpg";

		$this->set("original", $original);
		$this->set("sidebar", 1);
		$this->set("order", $order);

		$this->set("form", true);
		$this->set("step", 4);
		
		
		$this->set("issuer", $this->_idealList());
	} else {

		$this->set("form", false);
		$this->issuer = $this->data["Order"]["issuer"];
		$this->data =$this->Order->find("first", array('conditions' => array('Order.id' => $id, 'Order.payment' => 0, "Order.status <=" => 19)));
	
		if ($this->data["Order"]["status"] <= 19) {	
			
			$return = $this->_idealRequest($id, (($this->data["Order"]["total"])/100));
		
			$this->data["Order"]["ideal_trxid"] = $return["trxid"];
			$this->data["Order"]["ideal_trxcode"] = $return["trxcode"];
			$this->data["Order"]["ideal_url"] = $return["url"];
			$this->data["Order"]["status"] = 15;
		
			$this->Order->save($this->data);
		
			$this->redirect($this->data["Order"]["ideal_url"]);
		} else {
	
		}
	}
}


function discount() {

	$id = $this->Session->read("order_id");
	
	
	$order = $this->Order->find("first", array('conditions' => array('Order.id' => $id)));
	
	if ($order) {
	
		if ($order["Order"]["discount"] == 'true') {
			
			$order["Order"]["discount"] = 'false';
			$order["Order"]["total"] = $order["Order"]["subtotal"] + $order["Order"]["shipping"];
		} else {
		
			$order["Order"]["discount"] = 'true';
			$order["Order"]["total"] = $order["Order"]["subtotal"] + $order["Order"]["shipping"] - 150;
		}
		
		
		$this->Order->save($order);
		$this->set("order", $order);	
	}
	
	
	$this->layout="ajax";

}


function payment() {
	$this->data["Order"]["ideal_trxid"] = $_GET["trxid"];
	$this->data["Order"]["ideal_trxcode"] = $_GET["ec"];
	//$this->data["Order"]["payment"] = 0;
	
	$this->data = $this->Order->find("first", array('conditions' => array($this->data["Order"])));
		
	if ($this->data) {
		if ($this->data["Order"]["payment"] != 1) {
		//$this->_idealStatus($this->data["Order"]["ideal_trxid"]);
		switch ($this->_idealStatus($this->data["Order"]["ideal_trxid"])) {
			case "SUCCESS":
				// payment ok, save in db and reroute to succes page
			
				$this->data["Order"]["payment"] = 1;
				$this->data["Order"]["status"] = 19;
				$this->data["Order"]["secret"] = uniqid();
				
				$order = $this->Order->save($this->data);
				//debug($order);
				
				$this->Email->to = $this->data["Order"]["email"];
   				$this->Email->bcc = array('dennis.vandervliet@gmail.com');  
    			$this->Email->subject = 'Vriendenposter.nl: bevestiging bestelling nr ' . $this->data["Order"]["id"];
    			$this->Email->replyTo = 'klantenservice@vriendenposter.nl';
    			$this->Email->from = 'Vriendenposter <klantenservice@vriendenposter.nl>';
    			$this->Email->template = 'confirmation'; // note no '.ctp'
    			//Send as 'html', 'text' or 'both' (default is 'text')
    			$this->Email->sendAs = 'html'; // because we like to send pretty mail
    			//Set view variables as normal
    			
    			//Do not pass any args to send()
    			$email = array(
    				'fullname' => $this->data["Order"]["firstname"] . " " . $this->data["Order"]["lastname"],
    				'thankyou_text' => 'Bedakt',
    				'company' => $this->data["Order"]["company"],
    				'street' => $this->data["Order"]["street"] . " " . $this->data["Order"]["streetnr"],
    				'city' => $this->data["Order"]["zipcode"] . " " . $this->data["Order"]["city"],
    				'subtotal' => $this->data["Order"]["subtotal"],
    				'total' => $this->data["Order"]["total"],   			
    				'shipping' => $this->data["Order"]["shipping"],
    				'discount' =>  $this->data["Order"]["discount"],
    				'excl_tax' =>   ($this->data["Order"]["total"]/119)*100,
    				'tax' => ($this->data["Order"]["total"]/119)*19,
    				'closing' => 'blabla',
    				'url'=> 'ddd',
    				'logo' => 'ddd',
    				'alt_text' => 'test',
    				'order_number' => $this->Order->id,
    				'order_date' => $this->data["Order"]["created"]);
    				
    				
    				
				$this->set('data', $email);    		
    			
    
    			
    			$this->Email->send();
				$this->redo($this->data["Order"]["id"], 0, false);
    			
    			

				
		
				$this->set('succes', true);
				break;
			case  "CANCELLED":
				$code = $this->data["Order"]["example_id"];
				$this->set('succes', false);
				$this->set('code', $code);
				//$this->succes();
				//exit;
				break;
			default :
				$code = $this->data["Order"]["example_id"];
				$this->set('succes', false);
				$this->set('code', $code);
				//$this->succes();
				//exit;
				break;
		
		
		}
	} else {


    			
		
		$code = "already payed";
		$this->set('succes', false);
		$this->set('code', $code);$code = "already payed";
		$this->set('succes', false);
		$this->set('code', $code);
	
	
	}
	} else {
		$this->data["Order"]["ideal_trxid"] = $_GET["trxid"];
		$this->data["Order"]["ideal_trxcode"] = $_GET["ec"];

		$this->data = $this->Order->find("first", array('conditions' => array($this->data["Order"])));
		if ($this->data["Order"]["payment"] == 1) {
				$code = "already payed";
				$this->set('succes', false);
				$this->set('code', $code);
				//$this->succes();
				//exit;
		} else {
				$code = "no transaction found";
				$this->set('succes', false);
				$this->set('code', $code);
				//$this->succes();
				//exit;
		}
	}
	
	
}

function _idealStatus($trxid) {
	
	require_once($this->idealLib . "ideal.cfg.php");
	require_once($this->idealLib . "ideal.lib.php");
	require_once($this->idealLib . "ideal.utils.php");
	
	
	$oStatusRequest = new StatusRequest();
	$oStatusRequest->setTransactionId($trxid);
	
	$aTransactionData['transaction_status'] = $oStatusRequest->doRequest();

	if($oStatusRequest->hasErrors())	{
		ideal_output('<pre>' . var_export($oStatusRequest->getErrors(), true) . '</pre>');
	} else {
	
		return $aTransactionData['transaction_status'];
	}


}

function _idealRequest($id, $amount) {
	require_once($this->idealLib . "ideal.cfg.php");
	require_once($this->idealLib . "ideal.lib.php");
	require_once($this->idealLib . "ideal.utils.php");
	
	$sTransactionCode = randomCode(32);
	
	$sIssuerId = $this->issuer;
	$sCustomerId = $id;
	$fInvoiceAmount = number_format(floatval(str_replace(',', '.', $amount)), 2, '.', '');
	$sReturnUrl = 'http://www.vriendenprinter.nl/orders/payment/';

	$oTransactionRequest = new TransactionRequest();
	$oTransactionRequest->setOrderId(date('YmdHis'));
	$oTransactionRequest->setOrderDescription('Vriendenprinter.nl nr: ' . $sCustomerId);
	$oTransactionRequest->setOrderAmount($fInvoiceAmount);
	$oTransactionRequest->setIssuerId($sIssuerId);
	$oTransactionRequest->setEntranceCode($sTransactionCode);
	$oTransactionRequest->setReturnUrl($sReturnUrl);
		
	$sTransactionId = $oTransactionRequest->doRequest();

	if($oTransactionRequest->hasErrors()) {
		ideal_output('<pre>' . var_export($oTransactionRequest->getErrors(), true) . '</pre>');
	}
	
	
	$return = array(
		'trxid' => $sTransactionId,
		'trxcode' => $sTransactionCode,
		'url' => $oTransactionRequest->getTransactionUrl()
		);
	return $return;
}

function _idealList() {
	require_once($this->idealLib . "ideal.cfg.php");
	require_once($this->idealLib . "ideal.lib.php");
	require_once($this->idealLib . "ideal.utils.php");

	$oIssuerRequest = new IssuerRequest();
	$aIssuerList = $oIssuerRequest->doRequest();
	$sIssuerList = '';
	
	if($oIssuerRequest->hasErrors())	{
		ideal_output('<pre>' . var_export($oIssuerRequest->getErrors(), true) . '</pre>');
		return $false;
	} else { 
		return $aIssuerList;
	}
		
}

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

function _directoryJob($photo, $facebookid, $callback = NULL) {
	//echo $facebookid;
	$payload = array(
			'array' => $photo,
			'facebookId' => $facebookid,
			'type' => 'dir',
			'callBackUrlSuccess' => $callback);
			
		$options = array('tube' => $this->_allocateTube('mon'));
			
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




function succes() {

$this->set('succes', true);
}

function view () {
	if (isset($this->boss)) {
		$this->paginate = array(
        'order' => array("Order.id DESC"),        'limit' => 10
    );
	} else {
$this->paginate = array(
        'conditions' => array('Order.facebookid' => $this->facebookInfo["id"]),
        'order' => array("Order.id DESC"),        'limit' => 10
    );
	}
    $orders = $this->paginate('Order');
    $this->set(compact('orders'));



/*	$orders = $this->Order->find("all", array("conditions" => array("Order.facebookid" => $this->facebookInfo["id"]), "order" => "Order.id DESC"));
*/	$this->set("orders" , $orders);
	$this->set("step" , 1);
	$this->set("admin", true);
	$this->set("sidebar", 1);
	$this->set("section", "overview");
}

function confirm ($order, $status = NULL, $secret = NULL) {
	// this function will confirm the existence of the poster
	// change the status of the poster to created
	
	
	//if ($_SERVER["REMOTE_ADDR"] == $this->allowedIp){
	$count = $this->Order->find("count", array("conditions"=> array("Order.id" => $order) ));
	
	
	
	if ($count == 1) {
		//$this->Poster->set("status", $status);
	
		$this->Order->updateAll(array('Order.status' => $status), array('Order.id' => $order));	
		exit;
		return true;
		
	} else {
		echo "no valid confirm";
		exit;
		return false;
	}


}

function dispatch($id) {
	//
	$this->Order->updateAll(array('Order.status' => 61), array('Order.id' => $id));
	
	$order = $this->Order->find("first", array("conditions" => array("Order.id" => $id)));
	
	
	$orderAtt = "ORDER INFO";	
	file_put_contents("/var/facebook/". $id . ".txt", $orderAtt);
	
	$files = array(0 => array("dest" => $id . "order.jpg", "source" => "/var/facebook/final/" .$order["Order"]["facebookid"] . "-". $id . ".jpg" ),
			1 => array("dest" =>$id . ".txt", "source" => "/var/facebook/". $id . ".txt" )
	);
	
	$this->Jobs->_ftpJob($files, $this->Jobs->_updateStatus("order", $id, 70, $order["Order"]["secret"]));

	$this->Session->setFlash("De bestelling is vertuurd naar de afdrukcentrale", 'notice');
	$this->redirect("/orders/view");

}

function redo ($id, $screen = 0, $redirect = true) {


	$data = $this->Order->findById($id);
	//debug($data);
	if ($screen == 1) {
		$array = array(
		"tilemargin" => 0.1,
		"canvas" => "1080px",
		"count" => count(unserialize($data["Example"]["photos"])),
		"format" => 'landscape',
		"facebookid" => $data["Example"]["facebookid"],
		"bgcolor" => $data["Example"]["bgcolor"],
		"min" => 1.78,
		"max" => 1.78,
		"cutting" => false,
		"photos" => unserialize($data["Example"]["photos"]),
		"order_id" => $id,
		"secret" => uniqid()
		);
	
	
	} else {
	$array = array(
		"tilemargin" => $data["Example"]["tilemargin"],
		"canvas" => 750,
		"count" => count(unserialize($data["Example"]["photos"])),
		"format" => $data["Example"]["format"],
		"facebookid" => $data["Example"]["facebookid"],
		"bgcolor" => $data["Example"]["bgcolor"],
		"min" => Configure::read("minRatio"),
		"max" => Configure::read("maxRatio"),
		"cutting" => true,
		"photos" => unserialize($data["Example"]["photos"]),
		"order_id" => $id,
		"secret" => uniqid()
		);
	}
	$cmd = $this->Montage->generateFinalProduct($array);

	$array["original"] = str_replace("[ID]", $data["Order"]["id"],$cmd["final"] . ".pdf");
	
	$array["thumbnail"] = str_replace("[ID]", $data["Order"]["id"],$cmd["final"] . "thumb.jpg");
	
	$array["crop"] = str_replace("[ID]", $data["Order"]["id"],$cmd["final"] . "crop.jpg");
	
	$array["photos"] = serialize($array["photos"]);
	
	$array["canvasx"] = $cmd["canvas_x"];
	$array["canvasy"] = $cmd["canvas_y"];
	$this->Order->Montage->saveAll($array);
	
	
	
	$data = $this->Order->Montage->findById($this->Order->Montage->id);
		
	$this->Jobs->_bigJob($cmd["convert"], $this->_updateStatus("Montage", $data["Montage"]["id"], 30,$data["Montage"]["secret"] ) );
	$this->Jobs->_bigJob(str_replace("[ID]", $id,$cmd["montage"]), $this->_updateStatus("Montage", $data["Montage"]["id"], 40,$data["Montage"]["secret"] ) );
		
	$this->Jobs->_bigJob(str_replace("[ID]", $id,$cmd["border"]), $this->_updateStatus("Montage", $data["Montage"]["id"], 50,$data["Montage"]["secret"] ) );
		
	$this->Jobs->_bigJob(str_replace("[ID]", $id,$cmd["pdf"]), $this->_updateStatus("Montage", $data["Montage"]["id"], 60,$data["Montage"]["secret"] ) );
	$this->Jobs->_bigJob(str_replace("[ID]", $id,$cmd["thumb"]), $this->_updateStatus("Montage", $data["Montage"]["id"], 60,$data["Montage"]["secret"] ) );
	$this->Jobs->_bigJob(str_replace("[ID]", $id,$cmd["30m"]), $this->_updateStatus("Montage", $data["Montage"]["id"], 60,$data["Montage"]["secret"] ) );
	
	if ($redirect == true) {
		$this->redirect("/orders/view");
	
	} else {
	return true;	
	}
}


}
?>