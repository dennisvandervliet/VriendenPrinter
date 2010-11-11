<?php

	// Create a random code with N digits.
	function randomCode($iLength = 64)
	{
		$aCharacters = array('a', 'b', 'c', 'd', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

		$sResult = '';

		for($i = 0; $i < $iLength; $i++) // (62 ^ [$digits] mogelijke codes)
		{
			$sResult .= $aCharacters[rand(0, sizeof($aCharacters) - 1)];
		}

		return $sResult;
	}

	// Retrieve ROOT url of script
	function getRootUrl($iParent = 0)
	{
		$sRootUrl = '';

		if(isset($_SERVER['SERVER_PROTOCOL']))
		{
			$sRootUrl .= strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'))) . '://';
		}
		else
		{
			$sRootUrl .= 'http://';
		}

		$sRootUrl .= $_SERVER['HTTP_HOST'];

		if(isset($_SERVER['SERVER_PORT']) && strcmp($_SERVER['SERVER_PORT'], '80') !== 0)
		{
			$sRootUrl .= ':' . $_SERVER['SERVER_PORT'];
		}

		$sRootUrl .= '/';

		if(isset($_SERVER['SCRIPT_NAME']))
		{
			$a = explode('/', substr($_SERVER['SCRIPT_NAME'], 1));

			while(sizeof($a) > ($iParent + 1))
			{
				$sRootUrl .= $a[0] . '/';
				array_shift($a);
			}
		}

		return $sRootUrl;
	}

	// Output iDEAL error/message
	function ideal_output($html)
	{
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<style type="text/css">

html, body, form, div
{
	margin: 0px;
	padding: 0px;
}

div.wrapper
{
	padding: 50px 0px 0px 0px;
	text-align: center;
}

p, td, li
{
	font-family: Arial;
	font-size: 15px;
}

		</style>

	</head>
	<body>

		<div class="wrapper">
			<p><img alt="iDEAL" border="0" src="images/ideal.gif"></p>

' . $html . '

		</div>

	</body>
</html>';

		exit;
	}

	// Read content from file
	function readFromFile($sPath)
	{
		if(file_exists($sPath) == false)
		{
			return '';
		}

		return file_get_contents($sPath);
	}

	// Write content to file
	function writeToFile($sPath, $sContent, $bClearFile = false)
	{
		if(file_exists($sPath) == false)
		{
			// Create file
			touch($sPath);

			// When creating a new file, we update file mode 
			// to avoid access problems with other tools like FTP.
			chmod($sPath, 0777);
		}

		if($bClearFile)
		{
			// Override file contents
			file_put_contents($sPath, $sContent);
		}
		else
		{
			// Append content to file
			file_put_contents($sPath, $sContent, FILE_APPEND);
		}
	}

?>