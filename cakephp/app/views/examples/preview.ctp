
<div>
<h3>Preview</h3>
<? if($ready == false): ?>
<p>Er wordt een voorbeeld van je poster gemaakt. Zodra het voorbeeld klaar is zal het hier verschijnen.</p>
<br><br>
<center><img src="/img/loader.gif"></center>

<?
echo $javascript->codeBlock(
'var i=0;function dobla() {'. 	$ajax->remoteFunction(
		array(
				'url' => array(  'controller' => 'examples', 'action' => 'preview', $this->data["example"]["bgcolor"], $this->data["example"]["format"], $this->data["example"]["margin"]),
				'update' => 'preview'
		)) .'; } setTimeout("dobla()", 5000);');
?>  


 

<div class="clear"></div>
</div>
<? elseif ($example["status"] == 20): ?>
<?

if ($example["format"] == "landscape" && $example["status"] == 20) {
	$h = "50";
	$w = ceil(($example["width"]/$example["height"])*50);
} else {
	$w = "50";
	$h = ceil(($example["height"]/$example["width"])*50);

}
if ($example["bgcolor"] == "'#234785'") {
	$example["bgcolor"] = "FBblue";
}

?>



Klik op het voorbeeld voor een vergroting.
<br><br>
<a class="preview" href="http://www.vriendenprinter.nl/examples/view/<?= $example["id"]?>.jpg" >
<img src="http://www.vriendenprinter.nl/examples/view/<?= $example["id"]?>/test.jpg" width="300"></a>
<br><br>

<? if($example["format"] != "fixed") :?>
<p>Deze preview geeft een idee van hoe de poster er uit gaat zien. De kwaliteit van de echte poster zal uiteraard een stuk scherper zijn</p>
<p>Afgedrukt zal de poster <strong><?= $w ?> cm</strong> breed en <strong><?= $h?> cm</strong> hoog zijn.</p>
<p>
<a href="/orders/create/<?= $example["id"]?>"><img src="/img/ico/shopping-cart50.png" style="float:right;">Bestel deze poster</a>
</p>
<? else: ?>
<p>Dit is een voorbeeld van de wallpaper, <a href="/examples/wallpaper/<?=$example["id"]?>">download</a> een <strong>gratis</strong> fullsize versie</p>


<? endif;?>
<div class="clear"></div>
</div>
<? elseif ($example["status"] < 20) : ?>

Nog even geduld je voorbeeld is bijna klaar.


<? else: ?>

We maken je voorbeeld

<div class="clear"></div>
</div>

<? endif;?>
<script type="text/javascript">
 $(document).ready(function() {

	/* This is basic - uses default settings */
	
	$("a.preview").fancybox({
		'width'				: <?= $example["width"]?>,
				'height'			:  <?= $example["height"]?>,
				'autoScale'			: true,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe'

	});
});
</script> 