<?
$size = getimagesize($img);
$mime = $size['mime'];
header("Content-type: $mime"); 
header('Content-Length: ' . filesize($img));  
if (isset($noDownload)) {
header('Content-Disposition: filename="'. basename($img) . '"');
	
} else {
header('Content-Disposition: attachment; filename="'. basename($img) . '"');
}
readfile($img);  
echo strlen($img);
exit();  
?>