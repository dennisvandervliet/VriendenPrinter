<h1>Stap 1: Verzamel al je vrienden</h1>

<p>
Om al je Facebook-vrienden op een poster af te beelden, is het allereerst nodig om in te loggen met je Facebook account. Wij verzamelen alle foto's van je vrienden en maken er een prachtige poster van [link naar voorbeeld]</p>

<h2>Wat doen jullie met mijn gegevens?</h2>

<p>We halen de de foto's op, gebruiken die eenmalig om er een poster van te maken en daarna verwijderen we alle persoonlijke informatie. We gaan geen ongewenste e-mails sturen en we vallen je Facebook-vrienden ook niet lastig.</p>

<h2>Hoe log ik in via Facebook?</h2>

<p >Heel simpel, via de knop hieronder</p>

<p>
<br>
<?= $facebook->login(array('size' => 'large', 'perms' => 'email,friends_photos', 'redirect' => '/posters/create')) ?>
</p>