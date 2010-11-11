<? if($succes == true):?>
<h1>Bedankt</h1>
<p >Je bestelling is in goede orde ontvangen en de betaling is ook voltooid.</p>
<p >We gaan nu een poster samenstellen en afdrukken, zodra de poster verstuurd is ontvang je e-mail met daarin een link om je bestelling te volgen.</p>
<h2>Status van je order</h2>
<p>Status van je order is via <a href="/orders/view">deze</a> pagina te volgen.</p>
<h2>Vragen?</h2>

<p >Heb je nog vragen neem dan contact op met de <a href="/pages/contact">klantenservice</a>, ik help je graag.</p>
<h2>Wil jij ook bij andere mensen aan de muur hangen?</h2>
<div id="like">
<p >Beveel vriendenprinter dan aan op Facebook <?= $facebook->like()?> </p>

</div>
<? else: ?>
<h1>Oepxdds</h1>
<p > Er is iets niet goed gegaan met de betaling van je bestelling, onze excuses hiervoor.</p>
<p>Er is tot nu toe ook nog geen geld afgeschreven van je bank of giro rekening.</p>
<p>Dit kan komen door een aantal dingen:</p>
<ul>
<li><p>Je hebt zelf de betaling geannuleerd, wil je alsnog bestellen ga dan naar het <a href="/orders/create/<?= $code?>">bestelformulier</a></p></li>
<li><p>De iDeal sessie is verlopen, voor de veiligheid verloopt na een aantal minuten de iDeal sessie. Via het <a href="/orders/create/<?= $code?>">bestelformulier</a> kun je een nieuwe bestelling aan maken.</p></li>

<li><p>Er gaat iets anders niet goed, neem <a href="/pages/contact">contact</a> op en ik help je verder.</p></li>
</ul>
<? endif;?>