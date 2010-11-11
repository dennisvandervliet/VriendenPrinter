<?
$size = getimagesize($img);
$mime = $size['mime'];
header("Content-type: $mime"); 
header('Content-Length: ' . filesize($img));  
header('Content-Disposition: attachment; filename="'. basename($img) . '"');
//print_r($size);
readfile($img);  
exit();  
?>