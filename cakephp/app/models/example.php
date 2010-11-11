<?php
class Example extends AppModel {    
var $name = 'Example';
var $belongsTo = array('Poster');
var $hasMany = array("Order");
}
?>