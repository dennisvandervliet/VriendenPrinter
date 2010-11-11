<script type="text/javascript">
$(function () {
    $('#selectall').toggle(
        function() {
            $('#friendlist').find(':checkbox').attr('checked', 'checked');
        },
        function() {
            $('#friendlist').find(':checkbox').attr('checked', '');
        }
    );
});

$(document).ready(function() {
  $('#friendlist li').click(function(event) {
 if (event.target.type !== 'checkbox') {
   $(':checkbox', this).trigger('click');
 }
  });
});

</script>

<h1>Stap 2: Selecteer je vrienden</h1>
<p>VriendenPrinter verzamelt in eek keert foto's van al je vrieden en drukt ze met elkaar af op een grote poster.</p>
<p>Vind je sommige niet zo fotogeniek, dan kun je die fotootjes simpel weglaten door het vinkje weg te halen.</p>
<p>Je kunt ook met een schone lei beginnen.</p>
<p><a href="#" id="selectall">Begin met een schone lei</a></p>

<?php echo $this->Form->create('friends', array('url' => '/posters/build', 'id' => 'friends')); ?>

<?= $this->Form->submit("Klaar? Ga verder en bekijk je poster")?>
<div class="clear"></div>
<ul class="friendlist" id="friendlist">

<? $x = 0;?>
<? foreach ($friends as $friend):?>
<? $x++; $class = "";?>
<? if ($x == 4) { $x = 0; $class = "break";}?>
<li class="friendlist <?=$class?>">
<img class="square-profile-image friendlist" src="<?= $friend["pic_square"]?>">
<div class="friendlist">
<?= $this->Form->checkbox($friend["id"], array("hiddenField" => false, "checked" => true))?>

<p><?= $friend["name"]?> </p>
</div>
</li>

<? endforeach;?>
</ul>

<?= $this->Form->submit("Klaar? Ga verder en bekijk je poster")?>
<?php echo $this->Form->end();?>
