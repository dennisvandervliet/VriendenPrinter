<?

function determineSize($count_x, $count_y, $canvas = 3000, $margin, $landscape = false) {
	// canvas value is x axis
	
	if($landscape) {
		$x_size = floor($canvas / ($count_x + ($margin *($count_x +1))));
		
		$margin_tile =  floor($margin * $x_size);
		
		
		$canvas_y = ($count_x * $x_size) + ($margin_tile * ($count_x + 1));	
		
		$margin_canvas = floor(($canvas - $canvas_y) / 2);	
		
		$canvas_y = $canvas_y + (2* $margin_canvas);
		$canvas_x = (2 * $margin_canvas) + ($count_x * $x_size) + ($margin_tile * ($count_x + 1));	
	} else {
		$x_size = floor($canvas / ($count_x + ($margin *($count_x +1))));	
	
		$margin_tile =  floor($margin * $x_size);
	
		$canvas_y = ($count_y * $x_size) + ($margin_tile * ($count_y + 1));
		
		$margin_canvas = floor(($canvas - $canvas_y) / 2);
		
		$canvas_y = $canvas_y + (2* $margin_canvas);
		$canvas_x = (2 * $margin_canvas) + ($count_x * $x_size) + ($margin_tile * ($count_x + 1));
		
	}
	if ($canvas > 2000) {
	$margin_tile = floor($margin_tile /2); // correct
	}
	
	
	if($landscape) {
		$return = array(
		"size_x" => $x_size,
		"size_y" => $x_size,
		"margin_tile" => $margin_tile,
		"margin_canvas" => $margin_canvas,
		"canvas_x" => $canvas_y,
		"canvas_y" => $canvas_x);
	
	
	} else {
		$return = array(
		"size_x" => $x_size,
		"size_y" => $x_size,
		"margin_tile" => $margin_tile,
		"margin_canvas" => $margin_canvas,
		"canvas_x" => $canvas_x,
		"canvas_y" => $canvas_y);
		
	}	
	debug($return);
	return $return;

}
function determineDimensions($n = 16, $min = 1.1, $max = 3, $canvas = 6000, $margin = 0.1 ) {
	// n = number of pictures in the montage
	//$n = 234;
	
	
	
	$penalty = (ceil(sqrt($n)) * ceil(sqrt($n))) - $n;
	$count_x = ceil(sqrt($n));
	$count_y = ceil(sqrt($n));
		
		

		
		$return["square"] = array("count_x" => $count_x, "count_y" => $count_y, "ratio" => 1, "penalty" => $penalty);
		
		$return["square"] = array_merge($return["square"], $this->determineSize($count_x, $count_y, $canvas, $margin));
			
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
			$return["landscape"]= array("count_x" => $count_y, "count_y" => $count_x, "ratio" => $count_y/$count_x, "penalty" => $penalty);
			$return["landscape"] = array_merge($return["landscape"], $this->determineSize($count_x, $count_y, $canvas, $margin, true));
			
			
			$return["portrait"] = array();
			$return["portrait"]= array("count_x" => $count_x, "count_y" => $count_y, "ratio" => $count_y/$count_x, "penalty" => $penalty);
			$return["portrait"] = array_merge($return["portrait"], $this->determineSize($count_y, $count_x, $canvas, $margin));
				
			$low = $penalty;
			}
		}
	}
	
	return $return;
}
?>

