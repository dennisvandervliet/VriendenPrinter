<ul>
<? $cat = "" ?>
<? foreach ($faqs as $faq): ?>

<? if ($cat ==  $faq["Faq"]["cat"] ) :?>
<? else: ?>
	</ul><h1><?= $faq["Faq"]["cat"] ?></h1><ul>
	<? $cat = $faq["Faq"]["cat"] ?>
<? endif; ?>

<h2><?= $faq["Faq"]["question"] ?></h2>
<a name="<?= $faq["Faq"]["short"]?>"></a>
<p><?= nl2br($faq["Faq"]["answer"]) ?></p>

<? endforeach; ?>