<?
if (isset($update)) {

	
} else {
	$format = array("square" => '', "portrait" => "", "landscape" => "", "fixed" => "checked='1'");
	$margin = array("none" => "checked='1'", "small" => "", "big" => "");
	$bgcolor = array("white" => "checked='1'", "black" => "", "FBblue" => "");
	
	$data = array("format" => $format,
					"margin" => $margin,
					"bgcolor" => $bgcolor);


}

?>
<div id="form">
<?= $this->Form->create("example", array("id" => "example_pimp", "name" => "example_pimp")) ?>
<?
if ($realtime == false) {
	echo $ajax->submit('Maak voorbeeld', array('url'=> array('controller'=>'examples', 'action'=>'preview'), 'update' => 'preview'));
}
?>
<div class="clear"></div>
<div class="form-box" id="format">
<h4>1. Kies een formaat:</h4>
<div id="tabs">
<ul>

	<li><a href="#wallpaper">Wallpaper (gratis)</a></li>
	<li><a href="#poster">Fotoposter</a></li>
</ul>

<div class="active" id="poster">
<ul class="horizontal">
	<li>
	<label for="ExampleRadioSquare"><img src="/img/ico/square.png" width="80"></label>
	<input type="radio" value="square" id="ExampleRadioSquare" name="data[example][format]" <?= $data['format']['square']?>
	<br><h4>Vierkant</h4>	
	<small>50 x 50 cm</small>	
	<li><label for="ExampleRadioPortrait"><img src="/img/ico/portrait.png" width="80"></label>
	<input type="radio" value="portrait" id="ExampleRadioPortrait" name="data[example][format]"  <?= $data['format']['portrait']?>
	<br><h4>Staand</h4>
	<small>50 x 100 cm</small>
	</li>
	<li><label for="ExampleRadioLandscape"><img src="/img/ico/landscape.png" width="80"></label>
	
	<input type="radio" value="landscape" id="ExampleRadioLandscape" name="data[example][format]"  <?= $data['format']['landscape']?>>
	<br><h4>Liggend</h4>
	<small>50 x 100 cm</small>	

	</li>
	</ul>
</div>




<div id="wallpaper">
	
<input type="radio" value="fixed" id="ExampleRadioFixed" name="data[example][format]"  <?= $data['format']['fixed']?>>
<img src="/img/wallpaper.png" style="float:right;">

	<input type="hidden" name="data[screenx]" id="screenx">
	<input type="hidden" name="data[screeny]" id="screeny">
	<h4>Wallpaper</h4>
	
	<p>De wallpaper heeft automatisch de juiste resolutie</p>
	<script type="text/javascript">
	document.example_pimp.screenx.value = screen.width;
	document.example_pimp.screeny.value = screen.height;
	
	
	
	</script>
</div>
</div>

<?= $ajax->tabs('tabs')?>
<? if (isset($what)) :?>

<script type="text/javascript">
 $(document).ready(function() {
 
 $('#tabs').tabs({ selected: 1 });
 
 });
</script>
<? endif; ?>



<div class="clear"></div>
</div>

<div class="form-box" id="margin">
<h4>2. Kies de hoeveelheid ruimte tussen de foto's:</h4>

<ul class="horizontal">
	<li>
	<label for="ExampleRadioNone"><img src="/examples/quick/white/0" width="80" height="80"></label>
	<input type="radio" value="0" id="ExampleRadioNone" name="data[example][margin]"  <?= $data['margin']['none']?>>
	<br><h4>Geen</h4>	
	</li>

	<li><label for="ExampleRadioSmall"><img src="/examples/quick/white/0.05" width="80"  height="80"></label>
	<input type="radio" value="0.05" id="ExampleRadioSmall" name="data[example][margin]"  <?= $data['margin']['small']?>>
	<br><h4>Kleine ruimte</h4>	
	
	</li>
	</li>
	<li><label for="ExampleRadioBig"><img src="/examples/quick/white/0.1" width="80"  height="80"></label>
	
	<input type="radio" value="0.15" id="ExampleRadioBig" name="data[example][margin]"  <?= $data['margin']['big']?>>
	<br><h4>Veel ruimte</h4>	
	
	</li>

	</li>
</ul>

<div class="clear"></div>

</div>





<div class="form-box" id="bgcolor">
<h4>3. Kies je achtergrondkleur:</h4>

<ul class="horizontal">
	<li>
	<label for="ExampleRadioWhite"><img src="/examples/quick/white/0.1" width="80" height="80"></label>
	<input type="radio" value="white" id="ExampleRadioWhite" name="data[example][bgcolor]"  <?= $data['bgcolor']['white']?>>
	<br><h4>Wit</h4>	
	</li>

	<li><label for="ExampleRadioBlack"><img src="/examples/quick/black/0.1" width="80"  height="80"></label>
	<input type="radio" value="black" id="ExampleRadioBlack" name="data[example][bgcolor]" <?= $data['bgcolor']['black']?>>
	<br><h4>Zwart</h4>	
	
	</li>
	</li>
	<li><label for="ExampleRadioFBblue"><img src="/examples/quick/234785/0.1" width="80"  height="80"></label>
	
	<input type="radio" value="FBblue" id="ExampleRadioFBblue" name="data[example][bgcolor]" <?= $data['bgcolor']['FBblue']?>>
	<br><h4>Facebook-blauw</h4>	
	
	</li>

	</li>
</ul>
<?php 
if ($realtime == true) {
echo $ajax->observeForm( 'example_pimp', 
    array(
        'url' => array( 'controller' => 'examples', 'action' => 'preview' ),
        'update'=> 'preview, dennis'
    ) 
); 
}
?>

<?
if ($realtime == false) {
	echo $ajax->submit('Maak voorbeeld', array('url'=> array('controller'=>'examples', 'action'=>'preview'), 'update' => 'preview'));
}
?>
<?= $this->Form->end()?>
</div>
</div>
<div id="preview">
<h3>Preview</h3>
<p>
Selecteer links de opties voor je poster en hier verschijnt een voorbeeld van je poster.
</p>
<div class="clear"></div>
</div>






