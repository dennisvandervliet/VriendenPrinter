<?
$marginText = array("0.0000" => "Geen ruimte", "0.0300" => "Kleine ruimte", "0.0500" => "Meer ruimte", "0.0700" => "Veel ruimte");
?>
<table class="preview_table">
<script>$(document).ready(function(){
$(".preview").colorbox();
});</script>
<tr>
<th></td>
<th>Facebook Blauw</th>
<th>Zwart</th>
<th>Wit</th>
</tr>
<? $more = false;$x=0;?>
<? foreach($examples as $example): ?>
<? $original = "http://85.17.250.216/user/".$facebookid."/examples/".$example["Example"]["id"].".jpg";
$thumb = $original . ".thumb.jpg";

?>
<? if ($x==0 or $x ==3 or $x ==6): ?>
<tr><td class="text"><?= $marginText[$example["Example"]['tilemargin']] ?></td>
<? endif;?>
<? if ($example['Example']['status'] != 20):?>

<? $more = true;?>
<td>Geen voorbeeld</td>
<? else: ?>
<td class="image">

<a class="preview" href="/examples/preview/<?= $example["Example"]["id"]?>"><img src="<?= $thumb ?>"></a></td>
<? endif; ?>
<? if ($x==2 or $x == 5 or $x == 8): ?>
</tr>
<? endif;?>
<? $x++; ?>
<? endforeach; ?>


</table>
<? if ($more == true): ?>
<p class="text">
Nog even geduld er komen nog andere voorbeelden aan.
</p>
<? endif;?>
