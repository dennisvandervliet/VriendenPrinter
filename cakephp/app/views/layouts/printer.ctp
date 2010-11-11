<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		VriendenPrinter.nl | <?php echo $title_for_layout; ?>
	</title>
	
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>

	<script type="text/javascript" src="/js/fancy.js"></script>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('css');
		echo $this->Html->css("/css/jquery-ui-1.8.6.custom.css");
		
		echo $scripts_for_layout;
	?>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
	<?= $facebook->html(); ?>
<link rel="stylesheet" href="/css/fancy.css" type="text/css" media="screen" />	
	<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19511211-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script type="text/javascript">
 $(document).ready(function() {

	/* This is basic - uses default settings */
	
	$("a#preview").fancybox({
				'width' : 950,
				'height': 475,
				
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe'

	});
	
		$("a.preview").fancybox({
				
				
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'image'

	});
	
});
</script>
</head>
<body>
	<div id="container">
		<div id="header">
		<div id="logo">
				<a href="http://www.vriendenprinter.nl"><img src="/img/vp_logo.png" width="300"></a>
							<?php echo $this->Session->flash(); ?>
		</div>

		<div id="social">
			<div id="social-like">
				<iframe allowtransparency="true" frameborder="0" scrolling="no"
        src="http://platform.twitter.com/widgets/tweet_button.html?via=vriendenprinter&url=http%3A%2F%2Fvriendenprinter.nl%2F&count=horizontal"
        style="width:130px;height:25px;"></iframe>
</div><div id="social-like" style="width: 130px;">
				<?= $facebook->like(array( 'font' => 'veranda', 'layout' => 'button_count', 'action' => 'recommend', 'colorscheme' => 'light', 'href' => 'http://www.vriendenprinter.nl'));?>
				</div>
		</div>
		<div id="header-links">
		<a href="/faqs/allevragen">FAQ</a>
		<a href="/pages/klantenservice">Klantenservice</a>
			<a href="/pages/contact">Contact</a>
			<a href="/pages/privacy">Privacy</a>
		</div>
	
		</div>

		
		<div id="content">
			<? if(isset($sidebar)): ?>
			<div id="sidebar" class="column">
				<ul class="steps">
				<li class="steps">
				<? if($facebookInfo["id"] == true): ?>
			
					<span style="float:right;">
					<?= $facebook->picture($facebookInfo["id"], array('size' =>'square'));?>
					</span>
					<p class="logged">Ingelogd als:<br>
					<?= $facebookInfo["name"]; ?><br>
					
					</p>
				<? else: ?>
				<p class="logged">Login in via Facebook
				<?= $facebook->login(array('perms' => 'email,friends_photos'));?>
				</p>
				<? endif;?>
				</li>
				<? if(isset($section) AND $section == "overview"): ?>
				<li class="steps <? if($step==1){ echo 'active';}?>"><p><a href="/orders/view">Bestellingen</a></p><p class="steps-label">Overzicht bestellingen</p></li>
				<li class="steps <? if($step==2){ echo 'active';}?>"><p><a href="/pages/contact">Contact</a></p><p class="steps-label">Stel een vraag</p></li>
				<? else: ?>
				<li class="steps <? if($step==1){ echo 'active';}?>"><p><a href="/posters/index">Stap 1</a></p><p class="steps-label">Login via Facebook</p></li>
				<li class="steps <? if($step==2){ echo 'active';}?>"><p><a href="/posters/create">Stap 2</a></p><p class="steps-label">Selecteer je vrienden</p></li>
				<li class="steps  <? if($step==3){ echo 'active';}?>"><p><a href="/posters/build">Stap 3</a></p><p class="steps-label">Pas je poster aan</p></li>
				<li class="steps  <? if($step==4){ echo 'active';}?>"><p><a href="/orders/create">Stap 4</a></p><p class="steps-label">Bestel je poster</p></li>
				<li><br>	<? if($facebookInfo["id"] == true): ?><?= $facebook->logout(array('redirect' =>'/posters/index/', 'label' => 'Logout'));?><? endif;?></li>
				<? endif;?>
				</ul>
		
		
			</div>
			<? elseif(!isset($col)) :?>
			
				<div id="sidebar" class="column">
				<div class="twitter">
				<h3><img src="/img/ico/twitter25.png"><a href="http://www.twitter.com/vriendenprinter" target="_blank">@vriendenprinter</a></h3>
				    <?     foreach ($tweets as $tweet) { ?> 
					<?
					$msg = preg_replace("/(http:\/\/)(.*?)\/([\w\.\/\&\=\?\-\,\:\;\#\_\~\%\+]*)/", "<a href=\"\\0\">\\0</a>", $tweet['title']); 
// link to users in replies 
$msg = preg_replace("(@([a-zA-Z0-9\_]+))", "<a href=\"http://www.twitter.com/\\1\">\\0</a>", $msg);
					?>
						<p class="twitter_date"><?=$this->Time->niceShort($tweet['pubDate']);?></p>
						<p class="twitter_msg"><?=$msg;?>						</p> 
					<?     } ?> 
				</div>
				
				</div>
			
			<? endif; ?>
			
			<? if (isset($col)): ?>
			
			<div id="col" class="column">
				<div id="main">
				<?php echo $content_for_layout; ?>
				</div>
			</div>	
			<? else : ?>
			
			<div id="main" class="column">
			
				<?php echo $content_for_layout; ?>
			</div>	
			<? endif; ?>
		
		</div>
		<div id="footer">
			<div id="footer-links">
					<img src="/img/vp_small_logo40.png">
			</div>
			<div id="footer-links">
			<ul>
			<li><a href="/faqs/allevragen">FAQ</a></li>
			<li><a href="/pages/klantenservice">Klantenservice</a></li>
			<li><a href="/pages/contact">Contact</a></li>
			<li><a href="/pages/privacy">Privacy</a></li>
			</ul>
			</div>
				<div id="facebook-login">
					<? if ($facebookInfo['id'] == true) {
						echo $facebook->logout();
					} else {
						//echo $facebook->login();
						
					}
	
				?>
				</div>
			
		</div>

		</div>

	<?php echo $this->element('sql_dump'); ?>
	
		<?= $facebook->init(); ?>
		
<script src='http://www.go2web20.net/twitterfollowbadge/1.0/badge.js' type='text/javascript'></script><script type='text/javascript' charset='utf-8'><!--
tfb.account = 'vriendenprinter';
tfb.label = 'follow-us';
tfb.color = '#234785';
tfb.side = 'l';
tfb.top = 136;
tfb.showbadge();
--></script>		
</body>
</html>