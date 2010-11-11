
<h1>Stap 4: Bestel je poster</h1>
<p class="text">
Vul onderstaand formulier volledig in, hierna kun je je bestelling bevestigen en betalen met iDeal</p>
<? if($form):?>

<?php echo $this->Form->create('Order', array('action' => 'create/')); ?>
<?= $this->Form->input('email', array('label' => 'E-mail', 'maxLength' => 100, 'value'=> $facebookInfo['email'])) ?>

<?= $this->Form->input('firstname', array('label' => 'Voornaam', 'maxLength' => 20, 'value'=> $facebookInfo['first_name'])) ?>
<?= $this->Form->input('lastname', array('label' => 'Achternaam', 'maxLength' => 20, 'value'=> $facebookInfo['last_name'])) ?>
<?= $this->Form->input('company', array('label' => 'Bedrijf', 'maxLength' => 20)) ?>
<?= $this->Form->input('street', array('label' => 'Straat', 'maxLength' => 30, "div" => array("id" => "street")))?>
<?= $this->Form->input('streetnr', array('label' => 'Nummer', 'maxLength' => 5, "div" => array("id" => "streetnr"))) ?>
<?= $this->Form->input('streetnrext', array('label' => 'Toevoeging', 'maxLength' => 5,"div" => array("id" => "streetnrext"))) ?>

<?= $this->Form->input('zipcode', array('label' => 'Postcode', 'maxLength' => 6,"div" => array("id" => "zipcode"))) ?>

<?= $this->Form->hidden( 'example_id', array( 'value' => $exampleId ) );?>
<?= $this->Form->input('city', array('label' => 'Plaats', 'maxLength' => 20,"div" => array("id" => "city"))) ?>


<?= $this->Form->input("agree", array('label' => "Ik ga akkoord met de algemene voorwaarden van Vriendeprinter.nl en bevestig hierbij dat ik geen copyright schend door het afdrukken van deze foto's", 'hidden' => false)) ?>

<?= $this->Form->submit('Ga verder') ?>
<?php echo $this->Form->end(); ?>
<? endif;?>
