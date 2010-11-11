<h1>Stap 4: Controleer en bevestig je bestelling</h1>


<div class="orderinfo check">
<h3 class="check">Controleer je gegevens</h3>
<ul class="orderinfo">
<li><p class="label">Naam</p>
<p class="info"><?= $order["Order"]["firstname"]?> <?= $order["Order"]["lastname"]?></p>
</li>
<li><p class="label">Straat + huisnummer</p>
<p class="info"><?= $order["Order"]["street"]?> <?= $order["Order"]["streetnr"]?> <?= $order["Order"]["streetnrext"]?></p>
</li>
<li><p class="label">Postcode + Stad</p>
<p class="info"><?= $order["Order"]["zipcode"]?> <?= $order["Order"]["city"]?></p>
</li>
<li><p class="label">E-mail</p>
<p class="info"><?= $order["Order"]["email"]?></p>
</li>
<li><p class="label">Voorbeeld poster</p>

<p class="info">
<a id="preview" href="<?= $original?>">
Bekijk voorbeeld</a></p>
</li>
</ul>

<p id="delivery"><a target="_new" href="/faqs/vraag/Levertijd">Hoe lang duurt het voor ik de poster in huis heb?</a></p>

</div>



<div id="price" class="price check">

<?= $this->element('price') ?>

</div>

<div class="orderinfo check">

<h3>Klopt alles?</h3>
<p>Als alles klopt kun je hieronder betalen, klopt er iets niet, <A HREF="javascript:history.go(-1)">pas dan je gegevens aan</A></p>
<br>



<h3>Betalen</h3>
<p>Betalen kan eenvoudig en snelmet iDeal, kies hieronder je bank en kies "Bevestig en betaal"</p>


<? if($form):?>
<?php echo $this->Form->create('Order', array('action' => 'check')); ?>
<br><p>Selecteer je bank:</p><br>
<? echo $this->Form->input('issuer', array('type' => 'select', 'label' => false, 'options' => $issuer, "div" => "issuer")); ?>


<?= $this->Form->submit('Bevestig en betaal') ?>

<?php echo $this->Form->end(); ?>

<? endif;?>
</p>
</div>