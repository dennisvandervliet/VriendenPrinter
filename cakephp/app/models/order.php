<?php
class Order extends AppModel {    
var $name = 'Order';
var $belongsTo = array('Example');
var $hasMany = array("Montage" => array("order" => "Montage.modified DESC"));
var $validate = array(
	'firstname' => array(
		'allowEmpty' => false,
		'rule' => array('custom', '/^[a-z0-9 ]*$/i')),
	'lastname' => array(
		'allowEmpty' => false,
		'rule' => array('custom', '/^[a-z0-9 ]*$/i')),
	'street' => array(
		'allowEmpty' => false,
		'rule' => array('custom', '/^[a-z0-9 ]*$/i'),
		'message' => 'Straat is verplicht'),
			
	'streetnr' => array(
		'allowEmpty' => false,
		'rule' => 'numeric',
		'message' => 'Alleen cijfers'),
			
	'streetnrext' => array(
		'allowEmpty' => true,
		'rule' => 'alphaNumeric'),
			
	'company' => array(
		'allowEmpty' => true,
		'rule' => 'alphaNumeric'),
			
			
	'zipcode' => array(
		'allowEmpty' => false,
		'rule' => 'alphaNumeric',	
'message' => 'Postcode is verplicht'),		
			
	'city' => array(
		'allowEmpty' => false,
		'rule' => 'alphaNumeric',
		'message' => 'Stad is verplicht'),
			
	'email' => array(
		'allowEmpty' => false,
		'rule' => 'email'),
	'agree' => array(
		'allowEmpty' => false,
		'rule' => array('equalTo', '1'),
		'message' => "Akkoord gaan met de Algemene Voorwaarden is verplicht")
	);			
	
}
?>