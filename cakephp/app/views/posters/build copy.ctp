
<h1>Stap 2: pimp je poster</h1>
<p class="text">
We maken nu zo snel mogelijk een aantal voorbeelden van je poster. Afhankelijk van de drukte kan dit even duren, een moment geduld dus.</p>
<p class="text">Zodra de voorbeelden klaar zijn verschijnen ze op deze pagina.</p>

<p class="text">Klik op een voorbeeld om een vergroting te zijn en verder te gaan met bestellen.</p>

<p id="post">
</p>
<p>
<?
echo $javascript->codeBlock(
'var i=0;function dobla() {'. 	$ajax->remoteFunction(
		array(
				'url' => array(  'controller' => 'examples', 'action' => 'updateExample', $data['Poster']['id'] ),
				'update' => 'post'
		)) .'; if (i <= 4) {setTimeout("dobla()", 7000);i++} else {} } dobla();');
?>  
</p> 
	
