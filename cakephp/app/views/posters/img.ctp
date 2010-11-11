<?
$size = getimagesize($img);
$mime = $size['mime'];
header("Content-type: $mime"); 
header('Content-Length: ' . filesize($img));  
//print_r($size);
readfile($img);  
exit();  
?>