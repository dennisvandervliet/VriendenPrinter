<?
class MontageComponent extends Object {
// holds all function related to finding the right size  for the montages

var $t;
var $q;
var $name = "Montage";
var $components = array("Jobs");
var $enabled = false;
var $cutting;





function determineSize($count_x, $count_y, $canvas = 3000, $margin, $landscape = false, $ratio = 0) {
	if (Configure::read("maxResolution") != 0 AND $ratio != 0) {
		$currentResolution = $canvas * ($ratio * $canvas);
		
		$ratioRes = $currentResolution / (Configure::read("maxResolution") * 1000000)*1.05;
		
		if($ratioRes >= 1) {
		
		$ratioRes = sqrt($ratioRes);
		
		$canvas = floor($canvas / $ratioRes);
		}
	}
	
	
	// canvas value is x axis
	if ($ratio != 0) {
	if ($landscape) {
		$canvas_y = $canvas;
		$canvas_x = round($ratio * $canvas_y);
		$size_y = floor($canvas_y / ($count_y + ($margin *($count_y ))));	
		$size_x = floor($canvas_x / ($count_x + ($margin *($count_x ))));
		if ($size_y > $size_x) {
			$x_size = $size_x;
		} else {
			$x_size = $size_y;
		}
		
		$margin_tile =  floor($margin * $x_size);
		if ($margin > 0 AND $margin_tile < 1) {
			$margin_tile=1;
		}
		$canvas_y = ($count_y * $x_size) + ($margin_tile * $count_y);
		//$canvas_x = ($count_x * $x_size) + ($margin_tile * $count_x);
		$margin_canvas = ceil(($canvas - $canvas_y) / 2);
		
		$canvas_y = $canvas_y + (2 * $margin_canvas);
		//$canvas_x = $canvas_x + (2 * $margin_canvas);
	} else {
		$canvas_x = $canvas;
		$canvas_y = round($ratio * $canvas);
		$size_y = floor($canvas_y / ($count_y + ($margin *($count_y ))));	
		$size_x = floor($canvas_x / ($count_x + ($margin *($count_x ))));
		if ($size_y > $size_x) {
			$x_size = $size_x;
		} else {
			$x_size = $size_y;
		}
		
		$margin_tile =  floor($margin * $x_size);
		if ($margin > 0 AND $margin_tile < 1) {
			$margin_tile=1;
		}
		$canvas_x = ($count_x * $x_size) + ($margin_tile * $count_x);
		//$canvas_y = ($count_y * $x_size) + ($margin_tile * $count_y);
		$margin_canvas = ceil(($canvas - $canvas_x) / 2);
		
		//$canvas_y = $canvas_y + (2 * $margin_canvas);
		$canvas_x = $canvas_x + (2 * $margin_canvas);
		
	}

	

	} else {	
	if($landscape) {
		$x_size = floor($canvas / ($count_y + ($margin *($count_y ))));	
		
		$margin_tile =  floor($margin * $x_size);
		if ($margin > 0 AND $margin_tile < 1) {
			$margin_tile=1;
		}
		
		$canvas_y = ($count_y * $x_size) + ($margin_tile * ($count_y ));	
		
		$margin_canvas = ceil(($canvas - $canvas_y) / 2);	
		if ($this->cutting == true) {
			$margin_canvas = $margin_canvas + 36;
		}
		$canvas_y = $canvas_y + (2* $margin_canvas);
		$canvas_x = (2 * $margin_canvas) + ($count_x * $x_size) + ($margin_tile * ($count_x));	
	} else {
		$x_size = floor($canvas / ($count_x + ($margin *($count_x ))));	
	
		$margin_tile =  floor($margin * $x_size);
		if ($margin > 0 AND $margin_tile < 1) {
			$margin_tile=1;
		}
		$canvas_x = ($count_x * $x_size) + ($margin_tile * ($count_x ));
		
		$margin_canvas = ceil(($canvas - $canvas_x) / 2);
		
		if ($this->cutting == true) {
			$margin_canvas = $margin_canvas + 36;
		}
		
		
		$canvas_x = $canvas_x + (2* $margin_canvas);
		$canvas_y = (2 * $margin_canvas) + ($count_y * $x_size) + ($margin_tile * ($count_y ));
		
	}

	}

	
	if($landscape) {
		$return = array(
		"size_x" => $x_size,
		"size_y" => $x_size,
		"margin_tile" => $margin_tile,
		"margin_canvas" => $margin_canvas,
		"canvas_x" => $canvas_x,
		"canvas_y" => $canvas_y);
	
	
	} else {
		$return = array(
		"size_x" => $x_size,
		"size_y" => $x_size,
		"margin_tile" => $margin_tile,
		"margin_canvas" => $margin_canvas,
		"canvas_x" => $canvas_x,
		"canvas_y" => $canvas_y);
		
	}	
	//debug($return);
	return $return;

}
function determineDimensions($n = 16, $min = 1.1, $max = 3, $canvas = 6000, $margin = 0.1 ) {


	if (preg_match("/px/", $canvas)) {
	
		$canvas	= str_replace("px", "", $canvas);
	

	} else {
		$canvas = ($canvas * 300)/ 25.4;
	}

	if ($min == $max) {
	
		$count_x = ceil(sqrt($n / $max));
		
		$count_y = ceil($max * $count_x);
		$penalty = ($count_x * $count_y)-$n;
		
		$return["fixed"] = array("name" => "fixed","count_x" => $count_x, "count_y" => $count_y, "ratio" => $count_x/$count_y, "penalty" => $penalty);
		
		$return["fixed"] = array_merge($return["fixed"], $this->determineSize($count_x, $count_y, $canvas, $margin, false, $max));
		
		$return["portrait"] = array("name" => "portrait","count_x" => $count_x, "count_y" => $count_y, "ratio" => $max, "penalty" => $penalty);
		
		$return["portrait"] = array_merge($return["portrait"], $this->determineSize($count_x, $count_y, $canvas, $margin, false, $max));
		
		$return["landscape"] = array("name" => "landscape","count_x" => $count_y, "count_y" => $count_x, "ratio" => $max, "penalty" => $penalty);
		
		$return["landscape"] = array_merge($return["landscape"], $this->determineSize($count_y, $count_x, $canvas, $margin, true, $max));
		

		$return["preview"] = $return["fixed"];

		$return["preview"]["name"] = "preview";
		//return $return;
		
	
	}
	
	
	
	$penalty = (ceil(sqrt($n)) * ceil(sqrt($n))) - $n;
	$count_x = ceil(sqrt($n));
	$count_y = ceil(sqrt($n));
		
		

		
		$return["square"] = array("name" => "square", "count_x" => $count_x, "count_y" => $count_y, "ratio" => 1, "penalty" => $penalty);
		
		$return["square"] = array_merge($return["square"], $this->determineSize($count_x, $count_y, $canvas, $margin));
	if  ($min == $max) {
		return $return;
	
	}		
	$low = 99999999;
	for ($x = ceil(sqrt($n)); $x >= 1; $x--) {
	
		$round = ceil($n / $x);
		
		$ratio = $round / $x;
		
		if ($min <= $ratio AND $ratio <= $max) {
		
			$count_x  = $x;
			$count_y = ceil($ratio * $x);
			$penalty = ($count_x * $count_y) - $n;
			
			if ($penalty < $low) {
			$return["landscape"] = array();
			$return["landscape"]= array("name" => "landscape", "count_x" => $count_y, "count_y" => $count_x, "ratio" => $count_y/$count_x, "penalty" => $penalty);
			$return["landscape"] = array_merge($return["landscape"], $this->determineSize($count_y, $count_x, $canvas, $margin, true));
		

			$return["portrait"] = array();
			$return["portrait"]= array("name" => "portrait","count_x" => $count_x, "count_y" => $count_y, "ratio" => $count_y/$count_x, "penalty" => $penalty);
			$return["portrait"] = array_merge($return["portrait"], $this->determineSize($count_x, $count_y, $canvas, $margin));
				
			$low = $penalty;
			}
		}
	}
	$return["preview"] = array("name" => "preview", "count_x" => 4, "count_y" => 4, "ratio" => 1, "penalty" => 0);
	$return["preview"] = array_merge($return["preview"], $this->determineSize(4, 4, 13, 0));
			
	
	
	return $return;
}

function im ($input, $output, $size = array(), $bgcolor = "white", $final, $photos) {
	
	$original = $input . $size["name"] . ".original.list";
	$bigthumbnail = $input . $size["name"] . ".bigthumbnail.list";
	$bigthumbnail_dir = $input . "bigthumbnails/";
	
	
	$cmd["convert"] = "cat " . $original . " | xargs -i$ /usr/local/bin/convert /var/facebook/base/originals/'$' ";
	


	//$file = "/var/facebook/base/originals/" . $photo["Photo"]["localfile"];
	
	//$cmd["convert"].= "/usr/local/bin/convert " . $file . " "; //will change to @-
	$cmd["convert"].= "-limit memory 256 -limit map 256 -limit area 256 ";
	$cmd["convert"].= "-resize x". $size["size_x"] . " ";
	$cmd["convert"].= "-resize '". $size["size_x"] . "<' ";
	$cmd["convert"].= "-gravity center ";
	$cmd["convert"].= "-crop " .$size["size_x"] . "x" . $size["size_x"] . "+0+0 +repage ";
	$cmd["convert"].= "-gravity southeast ";
	$cmd["convert"].= "-bordercolor ". $bgcolor . " ";
	
	$cmd["convert"].= "-border " . (($size["margin_tile"] / 2)) . "x" . (($size["margin_tile"]/2)) . " ";
	//$cmd.= "+repage ";
	$cmd["convert"].= "-quality 100% ";
	$cmd["convert"].= "-type TrueColor ";

	$cmd["convert"].= $bigthumbnail_dir  . "'$'\n";
	
	
	
	$geometry  = ($size["size_x"] + $size["margin_tile"]) . "x" . ($size["size_x"]  + $size["margin_tile"]) . "+0+0";
	$tile = $size["count_x"] . "x" . $size["count_y"];
	
	$cmd["montage"] = "cat " . $bigthumbnail . " | xargs -i$ /usr/local/bin/convert '$' miff: | ";
	$cmd["montage"] .= "/usr/local/bin/montage miff: ";



	$cmd["montage"].= "-limit memory 256m -limit map 256m -limit area 64m ";
	$cmd["montage"].= "-size ".$size["size_x"] . "x" . $size["size_y"] . " ";
	$cmd["montage"].= "-tile " . $tile . " ";
	$cmd["montage"].= "-geometry " . $geometry . " ";
	$cmd["montage"].= "-background ". $bgcolor . " ";
	$cmd["montage"].= "-density 300x300 ";
	$cmd["montage"].= "-quality 100% ";
	$cmd["montage"].= "-type TrueColor ";
	$cmd["montage"].= $final . " \n";
	
	
	
	
	$cmd["border"]= "/usr/local/bin/convert " . $final . " ";
	$cmd["border"].= "-mattecolor " . $bgcolor . " ";
	$cmd["border"].= "-frame ".  $size["margin_canvas"] . "x" . $size["margin_canvas"] . " ";
	$cmd["border"] .= "-page ". $size["canvas_x"] . "x" . $size["canvas_y"]. " ";
	$cmd["border"].= "-quality 100% ";
	$cmd["border"].= "-type TrueColor ";
	$cmd["border"].= $final . " \n";
	
	if (Configure::read("maxResolution") != 0) {
		$currentResolution = $size["canvas_x"] * $size["canvas_y"];
		
		$ratio = $currentResolution / (Configure::read("maxResolution") * 1000000);
		
		$ratio = sqrt($ratio);
		
		$size["canvas_x"] = round($size["canvas_x"] / $ratio);
		$size["canvas_y"] = round($size["canvas_y"] / $ratio);
	}
	$cmd["30m"] = "/usr/local/bin/convert " . $final . " ";
	$cmd["30m"].= "-quality 95% ";
	$cmd["30m"] .= $final . "30m.jpg\n";
	
	$cmd["pdf"] ="/usr/local/bin/convert " . $final . " ";
	$cmd["pdf"] .= "-page ". $size["canvas_x"] . "x" . $size["canvas_y"]. " ";
	$cmd["pdf"].= "-density 300x300 ";
	$cmd["pdf"].= "-quality 95% ";
	$cmd["pdf"] .= $final . ".pdf\n";
	
	
	$cmd["thumb"]= "/usr/local/bin/convert " . $final . " ";
	$cmd["thumb"].= "-quality 50% ";
	$cmd["thumb"].= "-resize 10% ";
	$cmd["thumb"].= $final . "thumb.jpg\n";	
	
	$cmd["thumb"].= "/usr/local/bin/convert " . $final . " ";
	$cmd["thumb"].= "-quality 50% ";
	$cmd["thumb"].= "-crop 4500x1800+0+0  ";
	$cmd["thumb"].= "-resize 30% ";

	$cmd["thumb"].= $final . "crop.jpg\n";	
	

	
	$cmd["final"] = $final;
	
	
	$cmd["canvas_x"] = $size["canvas_x"];
	$cmd["canvas_y"] = $size["canvas_y"];
	return $cmd;
	
}


function montage($input, $output, $size = array(), $bgcolor = "white", $return = false){
	
	$geometry  = $size["size_x"] . "x" . $size["size_y"] . "+" . $size["margin_tile"] . "+" . $size["margin_tile"];
	//$geometry  = "50+" . $size["margin_tile"] . "+" . $size["margin_tile"];
	$sizetest = $size["size_x"] . "x" . $size["size_y"] . " ";
	$tile = $size["count_x"] . "x" . $size["count_y"];
	
	$cmd = "cat " . $input . " | ";
	$cmd.= "/usr/local/bin/montage @- ";
	//$cmd.= $input . " "; // will change to @-
	$cmd.= "-tile " . $tile . " ";
	$cmd.= "-geometry " . $geometry . " ";
	$cmd.= "-size " . $sizetest;
	$cmd.= "-background ". $bgcolor . " ";
	$cmd.= "-quality 45% ";
	$cmd.= $output . "\n";


	if ($return == true) {
		return $cmd;
	} else {
		$this->Jobs->_queueJob($cmd);
	}	
	

}

function generateQuickView ($id, $photos_count) {
	//input will become linked to a list of files [/thumbnails/preview.list]
	$input = Configure::read("imageDir") . "user/" . $id . "/preview.thumbnail.list";
		
	$margins = array(0, "0.05", "0.1");
	$bgcolors = array("white", "black", "'#234785'");
	
	
	foreach ($margins as $margin) {
	
			foreach ($bgcolors as $bgcolor){
			
			$output = Configure::read("imageDir") . "user/" . $id . "/quick/quick-" . str_replace("#", "", $bgcolor).$margin. ".jpg";

			
			$size = $this->determineDimensions($photos_count, 1.1,  2, 13, $margin);
			
			$this->montage($input, $output, $size["square"], $bgcolor);
		
		}	
	}


}
function generateExample($array) {
	//input will become linked to a list of files [/thumbnails/{format}.list
	$input = Configure::read("imageDir") . "user/" . $array["facebookid"] . "/" .$array["format"] . ".thumbnail.list";
	$output = Configure::read("imageDir") . "user/" . $array["facebookid"] . "/examples/" . $array["id"] . ".jpg";
	
	if ($array["format"] == "landscape") {
		$canvas = 40;
	} else {
		$canvas = 40;
	}
	
	
	

	$size = $this->determineDimensions($array["count"],  $array["minRatio"], $array["maxRatio"], $canvas, $array["tilemargin"]);
	
	//debug($size[$array["format"]]);
	$big = $this->determineDimensions($array["count"], $array["minRatio"], $array["maxRatio"], $array["canvas"], $array["tilemargin"]);
	
	$this->Jobs->_directoryJob($array["photos"], $array["facebookid"], true, "", $size);
	
	if ($array["format"] == "fixed") {
		
		
		$array["format"] == "landscape";
	}
	
	
	$cmd = $this->montage($input, $output, $size[$array["format"]], $array["bgcolor"], true);
	//debug($cmd);
	$this->Jobs->_queueJob($cmd, $array["callBackUrl"], $array["id"], $array["facebookid"]);
	//input will become linke to a list of files
	// {originals/{format}.list
	$input = Configure::read("imageDir") . "user/" . $array["facebookid"] . "/";
	$output = Configure::read("imageDir") . "user/" . $array["facebookid"] . "/bigthumbnails/";
	$final = Configure::read("imageDir") . "final/" . $array["facebookid"] . "-" . "[ID].jpg";
	
	return $this->im($input, $output, $big[$array["format"]], $array["bgcolor"], $final, $array["photos"]);
	
}

function generateFinalProduct($array) {

	$this->cutting = $array["cutting"];
	
	$big = $this->determineDimensions($array["count"], $array["min"], $array["max"], $array["canvas"], $array["tilemargin"]);
	
	debug($big);
	$this->Jobs->_directoryJob($array["photos"], $array["facebookid"], true, "", $big, true);
	
	// input will become linked to a list of files
	$input = Configure::read("imageDir") . "user/" . $array["facebookid"] . "/";
	$output = Configure::read("imageDir") . "user/" . $array["facebookid"] . "/" . $array["format"] . ".bigthumbnail.list";
	$final = Configure::read("imageDir") . "final/" . $array["facebookid"] . "-" .$array["canvas"] .uniqid() .  "[ID].jpg";
	
	return $this->im($input, $output, $big[$array["format"]], $array["bgcolor"], $final, $array["photos"]);

}

}
?>
