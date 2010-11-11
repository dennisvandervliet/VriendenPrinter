<?
	if($montage["Montage"]["format"] = "landscape") {
		$x = $montage["Montage"]["canvasy"];
		$y = $montage["Montage"]["canvasx"];
	
	} else {
		$x = $montage["Montage"]["canvasx"];
		$y = $montage["Montage"]["canvasy"];
	
	}
$y = round( ($y / $x) * 500, 0) . "mm";
$x = "500mm";
?>
<h1>VriendenPrinter download</h1>


<h2>Versturen naar:</h2>
<p><?= $montage["Order"]["firstname"] ?> <?= $montage["Order"]["lastname"] ?></p>
<p><?= $montage["Order"]["street"] ?> <?= $montage["Order"]["streetnr"] ?> <?= $montage["Order"]["streetnrext"] ?></p>
<p><?= $montage["Order"]["zipcode"] ?> <?= $montage["Order"]["city"] ?></p>

<h2>Afdruk informatie:</h2>
<p>Formaat: <?= $montage["Montage"]["format"] ?> </p>
<p>Afmetingen: <?= $x ?> x <?= $y ?>  </p>

<h2>Bestand</h2>

<p><a href="/montages/pdf/<?= $montage["Montage"]["id"]?>/2">PDF</a>  </p>
<h2>Bevestig ontvangst</h2>

<h2>Track&Trace</h2>

<?= $this->Form->create("montage")?>
<?= $this->Form->input("Order.track_trace") ?>
<?= $this->Form->input("Montage.id", array("value" => $montage["Montage"]["id"] ))?>
<?= $this->Form->input("Montage.secret", array("value" => $montage["Montage"]["secret"], 'type'=> 'hidden' )) ?>

<?= $this->Form->Submit();?>
