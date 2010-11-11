<?

if (isset($try)) {
	switch($try) {
	
		case 1:
			$delay = 5;
		case 2:
			$delay = 5;
		default:
			$delay = 3;
	}
	$try++;
} else {
	$try = 0;
	$delay = 10;
}


echo $html->meta(null, null, array( 'http-equiv' => 'refresh', 'content' => $delay . ";http://www.vriendenprinter.nl/posters/build/". $id . "/". $try ), false);

?>
<h1>Een moment geduld</h1>
<p>De foto's van je vrienden worden gedownload, deze zijn nodig om voorbeelden te maken.</p>

<p>Binnen 15 sec sturen we je door naar de voorbeelden.</p>