<?php
/*********************************************
<!-- 
*   	DOTA ALLSTATS
*   
*	Developers: Reinert, Dag Morten, Netbrain, Billbabong.
*	Contact: developer@miq.no - Reinert
*
*	
*	Please see http://forum.codelain.com/index.php?topic=4752.0
*	and post your webpage there, so I know who's using it.
*
*	Files downloaded from http://dotastats.miq.no/download/
*
*	Copyright (C) 2009  Reinert, Dag Morten , Netbrain, Billbabong
*
*
*	This file is part of DOTA ALLSTATS.
*
* 
*	 DOTA ALLSTATS is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    DOTA ALLSTATS is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with DOTA ALLSTATS.  If not, see <http://www.gnu.org/licenses/>
*
-->
**********************************************/
require_once("functions.php");
require_once("config.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php print $botName; ?> Statistics</title>
<link rel="stylesheet" href="styles.css" type="text/css" />
<script type="text/javascript">
	function getClientWidth() {
	  return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
	}

	function getClientHeight() {
	  return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
	}
	function setWrapperDimensions()
	{
		setWrapperHeight();
		//setWrapperWidth();
		setHorizontalLoc();
	}
	function setWrapperHeight()
	{
		var windowheight = getClientHeight();
		var divh = document.getElementById('header').offsetHeight;
		var thheight = document.getElementById('theader').offsetHeight;
		if(document.getElementById('introtable') != null)
		{
			var introheight = document.getElementById('introtable').offsetHeight;
			document.getElementById('datawrapper').style.height = windowheight-divh-thheight-introheight+'px';
		}
		else
		{
			document.getElementById('datawrapper').style.height = windowheight-divh-thheight+'px';
		}
		
	}
	function setWrapperWidth()
	{
		var windowwidth = getClientWidth();
		var ninety = windowwidth*.9; 
		//document.getElementById('nav').style.width = ninety +'px';
		if(document.getElementById('data') != null)
		{
			document.getElementById('data').style.width = ninety +'px';
		}
		if(document.getElementById('theader') != null)
		{
			document.getElementById('theader').style.width = ninety+'px';
		}
		if(document.getElementById('introtable') != null)
		{
		document.getElementById('introtable').style.width = ninety+'px';
		}
	}
	function setHorizontalLoc()
	{
		var windowwidth = getClientWidth();
		if(document.getElementById('data') != null)
		{
			document.getElementById('data').style.left = (windowwidth*.05)+'px';
		}
		if(document.getElementById('theader') != null)
		{
			document.getElementById('theader').style.left = (windowwidth*.05)+'px';
		}
		if(document.getElementById('introtable') != null)
		{
		document.getElementById('introtable').style.left = (windowwidth*.05)+'px';
		}
	}
	
	function checkRange(numValue,numLow,numHigh){
		 if(isNaN(numValue)){
			  //alert("Please enter a valid number");
			  return false;
		 }else{
			  if(parseFloat(numValue)< numLow || parseFloat(numValue)>numHigh){
				   alert("Please enter a value between " + numLow + " and " + numHigh);
				   return false;
			  }
		 }
		 return true;
	}
	
	function gamesPlayed(numValue)
	{
		if(checkRange(numValue, 0, 5000))
		{
			var url = '?p=top&s=totalscore&o=desc&g='+numValue;
			location.href = url;
		}
	}
</script>

</head>

<body onresize="setWrapperDimensions()" onload="setWrapperDimensions()">
<div class="header" id="header">
		<div class="nav" id="nav">
					<ul>
						<!-- MENU -->
						  <li>
						  <a href=./>Home</a></li> 
						  <li> 
						  <a href=?p=top&s=totalscore&o=desc&g=<?php print $minGamesPlayed; ?>>Top Players</a></li> 
						  <li> 
						  <a href=?p=allusers&s=totgames&o=desc>Player Statistics</a></li> 
						  <li>
						  <a href=?p=heroall&s=description&o=asc>Hero Statistics</a></li> 
						  <li>
						  <a href=?p=games&s=datetime&o=desc>Game History</a></li> 
						  <li> 
						  <a href=?p=bans&s=name&o=asc>Bans</a></li>
						  <li> 
						  <a href=?p=admins&n=nolimit>Admins</a></li> 
						  <li>
							<form name="testForm" method=GET action="">
								<input type="text" 
								class="searchBox" 
								name="u" 
								onfocus="if(this.value=='Search for User') {this.value='';this.style.color='white';} else this.select();" 
								onblur="if(this.value==''){this.value='Search for User';this.style.color='#daa701';}" 
								value="Search for User"
								/>
								<input type="hidden" value="datetime" name="s" />
								<input type="hidden" value="desc" name="o" />
								<input type="hidden" value="user" name="p" />
							</form> 
						</li>
						<!-- END MENU -->
					</ul>	
		</div>
		
	<div class="clear"></div>
</div>
	<div id="wrap">
		<?php 		
		$valid_pages = array('intro', 
							'bans', 
							'games', 
							'allusers', 
							'user',
							'heroall',							
							'gameinfo',
							'hero', 
							'top', 
							'admins');

		if(!empty($_GET['p']))
		{
			if(in_array($_GET['p'], $valid_pages) && is_readable($_GET['p'] . '.php'))
			{
				include($_GET['p'] . '.php');
			}
			else if($_GET['p']=='')
			{
				include('intro.php');
			}
			else
			{
				echo 'Requested resource doesn\'t exist.';
			}
		}
		else
		{
			include('intro.php');
		}
		?>
	</div>
</body>
</html>
