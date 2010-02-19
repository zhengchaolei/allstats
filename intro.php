﻿<?php
/*********************************************
<!-- 
*   	DOTA ALLSTATS
*   
*	Developers: Reinert, Dag Morten, Netbrain, Billbabong, Boltergeist.
*	Contact: developer@miq.no - Reinert
*
*	
*	Please see http://www.codelain.com/forum/index.php?topic=4752.0
*	and post your webpage there, so I know who's using it.
*
*	Files downloaded from http://code.google.com/p/allstats/
*
*	Copyright (C) 2009-2010  Reinert, Dag Morten , Netbrain, Billbabong, Boltergeist
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
require_once("version.php");
?>
<div class="header" id="header">
</div>
<div class="pageholder" id="pageholder">
<center>
	<div id="theader">
	<table class="tableheader" id="tableheader">
  <tr>
	<td class="rowuh">
			<br>
			<br>
			<br>
			<h4>
			<?php print $phrase147." ".$botName." ".$phrase148; ?><br>
			</h4>
			<br>
			<br>
			<br>
	</td>
  </tr>
  </table>
	</div>
	<div id="datawrapper">
		<table class="table" id="data">
	<tr class="rowuh">
		<td>
			<br>
			<br>
			<br>
			<?php print $phrase149." ".$botName; ?> 
			<br>
			<br>
			<br>
			<?php print $phrase150; ?>
<?php

if ($verifytables) 
{
	if (checkDBTable("heroes") == 0) 
	{
?>
			<br>
			<br>
			<br>
			<div style="color:red">WARNING: "heroes" table not found. Please run allstats sql setup script first!</div>
<?php
	}

	if (checkDBTable("items") == 0) 
	{
?>
			<br>
			<br>
			<br>
			<div style="color:red">WARNING: "items" table not found. Please run allstats sql setup script first!</div>
<?php
	}

	if (checkDBTable("games") == 0 || checkDBTable("gameplayers") == 0 || checkDBTable("dotagames") == 0 || checkDBTable("dotaplayers") == 0 || checkDBTable("bans") == 0 || checkDBTable("admins") == 0) 
	{
?>
			<br>
			<br>
			<br>
			<div style="color:red">ERROR: ghost tables not found. Please check your configuration</div>
<?php
	}

	if ($includeImportedBans && checkDBTable("imported_bans") == 0) 
	{
?>
			<br>
			<br>
			<br>
			<div style="color:red">WARNING: "imported_bans" table not found. Please run allstats sql setup script first!</div>
<?php
	}
}
?>


		</td>
	</tr>
</table>
</div>
</center>
</div>
<div id="footer" class="footer">
  <h5> 
	 <?php print $phrase168;?>: <?php print VERSION;?> | <?php print VERSIONDATE;?>
  </h5>
</div>


