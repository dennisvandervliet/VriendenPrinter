<?php

class FaqsController extends AppController {

//var $scaffold;



function allevragen () {


	$faq = $this->Faq->find("all", array("order" => "cat DESC"));
	
	$this->set("faqs", $faq);
	//$this->set("col", true);

}


function vraag($cat) {

	$faq = $this->Faq->find("all", array("conditions" => array("Faq.cat" => $cat)));
	
	
	
	$this->set("faqs", $faq);
	//$this->set("col", true);
}
}


?>