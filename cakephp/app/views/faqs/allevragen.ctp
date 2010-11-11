<? $cat = "" ?>
<? foreach ($faqs as $faq): ?>

<? if ($cat ==  $faq["Faq"]["cat"] ) :?>
<? else: ?>
	</ul><h2><?= $faq["Faq"]["cat"] ?></h2><ul>
	<? $cat = $faq["Faq"]["cat"] ?>
<? endif; ?>

<li><a href="/faqs/vraag/<?= $faq["Faq"]["cat"] ?>#<?= $faq["Faq"]["short"] ?>"><?= $faq["Faq"]["question"] ?></a></li>



<? endforeach; ?>