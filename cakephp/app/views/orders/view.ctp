<?

$status = array(
	0 => "Onbekend",
	10 => "Aangemaakt",
	15 => "Betaling in verwerking",
	19 => "Betaling ontvangen",
	20 => "Bezig met montage",
	30 => "Bezig met montage",
	40 => "Bezig met montage",
	50 => "Bezig met montage",
	60 => "Montage voltooid",
	61 => "Onderweg naar afdrukcentrale",
	70 => "Verstuurd naar de afdrukcentrale",
	80 => "Verstuurd met de TNT");
?>


<script>$(document).ready(function(){
$(".preview").colorbox({width:"75%", height:"75%"});
});</script>
<? if($orders) :?>
<h1>Overzicht bestellingen</h1>
<table>
<tr id="tableheader">
<td>#</td>
<td>Datum</td>
<td colspan="2">Status</td>

</tr>
<? foreach($orders as $order): ?>
<tr id="tableheader">
<td><?= $order["Order"]["id"]?></td>
<td><?= $this->Time->niceShort($order["Order"]["created"])?></td>
<td colspan="2"><?= $status[$order["Order"]["status"]] ?></td>


</tr>
<? $z = 1;?>
<tr>
<td>
<? if(isset($boss)) :?>
<a href="/orders/redo/<?= $order["Order"]["id"]?>">Rd</a>
<? endif; ?>
</td>
<td>Verzendadres</td>
<td>Totaal</td>
<td>Track&Trace</td>
</tr>
<tr>
<td></td>
<td><small><?= $order["Order"]["firstname"]?> <?= $order["Order"]["lastname"]?><br>
<?= $order["Order"]["street"]?> <?= $order["Order"]["streetnr"]?> <?= $order["Order"]["streetnrext"]?><br>
<?= $order["Order"]["zipcode"]?> <?= $order["Order"]["city"]?></small></td>
<td><small><?= $this->Number->currency($order["Order"]["total"]/100 , "EUR")?><br>
<? if ($order["Order"]["payment"] == 1) { echo "Betaald"; } else { echo "Nog niet betaald";}?></small></td>
<td><a target="_blank" href="https://tracktrace.tntpostpakketservice.nl/Search/Searchbasic.aspx?B=<?= $order["Order"]["track_trace"]?>&P=<?= $order["Order"]["zipcode"]?>">Volg je bestelling</a></td>

</tr>



<? foreach ($order["Montage"] as $montage): ?>
<? if (isset($boss)): ?>
<tr>
<td><? if($boss == true) : ?><a href="/montages/sendout/<?= $montage["id"]?>">-></a><? endif;?></td>
<td># <?= $z?> - <?= $status[$montage["status"]] ?> <small><?= $this->Time->niceShort($montage["created"])?></small></td>
<? if($montage["status"] >= 60) :?>
<td><a href="/montages/view/<?= $montage["id"]?>">Thumb</a></td>
<td><? if($boss == true) : ?><a href="/montages/view/<?= $montage["id"]?>/2">Original</a><? endif;?></td>
<? else: ?>
<td></td>
<? endif;?>
</tr>
<? $z++?>

<? else: ?>
<? if ($montage["status"] >= 60): ?>
<tr>
<td></td>
<td><a href="/montages/view/<?= $montage["id"]?>">Voorbeeld</a></td>
</tr>
<? endif; ?>

<? endif;?>
<? endforeach;?>
<? endforeach;?>



<? endif;?>
</table>

<!-- Shows the page numbers -->
<?php echo $paginator->numbers(); ?>
<!-- Shows the next and previous links -->
<?php
	echo $paginator->prev('Ç Previous ', null, null, array('class' => 'disabled'));
	echo $paginator->next(' Next È', null, null, array('class' => 'disabled'));
?> 
<!-- prints X of Y, where X is current page and Y is number of pages -->
<?php echo $paginator->counter(); ?>