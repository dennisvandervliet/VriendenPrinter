<h3 class="check">Overzicht bestelling</h3>
<!--<div class="wallpost">
<?= $this->Form->checkbox('shareonwall', array('hiddenField' => false, 'checked' => $order["Order"]["discount"], 'onChange' => $ajax->remoteFunction( 
        array( 
            'url' => array( 'controller' => 'orders', 'action' => 'discount'), 
            'update' => 'price' 
        )  ) ));?>
<p class="discount">Ja, laat mijn Facebook vrienden weten dat ze binnenkort bij mij aan de muur hangen. Je krijgt dat automatisch 1,50 korting</p>
</div>
-->


<ul class="price">
<li id="base_price"><span class="total">Vriendenposter</span><span class="price"><?= $this->Number->currency($order["Order"]["subtotal"]/100, "EUR")?></p></span></li>
<li id="shipment"><span class="total">Verzendkosten</span><span class="price"><?= $this->Number->currency($order["Order"]["shipping"]/100, "EUR")?></span></li>
<? if ($order["Order"]["discount"] == 'true'): ?>
<li id="discount"><span class="total">Korting<br><p class="text"></p></span><span class="price"><?= $this->Number->currency(-150/100, "EUR", array("negative" => "-"));?></span></li>
<? endif;?>
<!--<li id="tax"><span class="total">Exclusief BTW</span><span class="price"><?= $this->Number->currency(($order["Order"]["total"]/119), "EUR")?></span></li>
<li id="tax"><span class="total">BTW (19%)</span><span class="price"><?= $this->Number->currency((($order["Order"]["total"]/119)/100)*19, "EUR")?></span></li>-->
<li id="total"><span class="total">Totaal</span><span class="price"><?= $this->Number->currency($order["Order"]["total"]/100 , "EUR")?></span></li>

</ul>
