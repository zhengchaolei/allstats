<?php
/*********************************************
<!-- 
*   	DOTA ALLSTATS
*   
*	Developers: Reinert, Dag Morten, Netbrain, Billbabong, Boltergeist.
*	Contact: developer@miq.no - Reinert
*
*	
*	Please see http://forum.codelain.com/index.php?topic=4752.0
*	and post your webpage there, so I know who's using it.
*
*	Files downloaded from http://code.google.com/p/allstats/
*
*	Copyright (C) 2009  Reinert, Dag Morten , Netbrain, Billbabong, Boltergeist
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
require_once("config.php");

function millisecondsToTime($milliseconds)//returns the time like 5.2 (5 seconds, 200 milliseconds)
{
	$return="";
	$return2="";
     // get the seconds
	$seconds = floor($milliseconds / 1000) ;
	$milliseconds = $milliseconds % 1000;
	$milliseconds = round($milliseconds/100,0);
	
	// get the minutes
	$minutes = floor($seconds / 60) ;
	$seconds_left = $seconds % 60 ;

	// get the hours
	$hours = floor($minutes / 60) ;
	$minutes_left = $minutes % 60 ;
// A little unneccasary with minutes and hours,,  but HEY  everythings possible
	if($hours)
	{
		$return ="$hours".":";
	}
	if($minutes_left)
	{
		$return2 ="$minutes_left".":";
	}
return $return.$return2.$seconds_left.".".$milliseconds;
}  

function secondsToTime($seconds)//Returns the time like 1:43:32
{
	$hours = floor($seconds/3600);
	$secondsRemaining = $seconds % 3600;
	
	$minutes = floor($secondsRemaining/60);
	$seconds_left = $secondsRemaining % 60;
	
	if($hours != 0)
	{
		if(strlen($minutes) == 1)
		{
		$minutes = "0".$minutes;
		}
		if(strlen($seconds_left) == 1)
		{
		$seconds_left = "0".$seconds_left;
		}
		return $hours.":".$minutes.":".$seconds_left;
	}
	else
	{
		if(strlen($seconds_left) == 1)
		{
		$seconds_left = "0".$seconds_left;
		}
		return $minutes.":".$seconds_left;
	}
}

function replayDuration($seconds)
{
	$minutes = floor($seconds/60);
	$seconds_left = $seconds % 60;
	
	if(strlen($seconds_left) == 1)
	{
	$seconds_left = "0".$seconds_left;
	}
	return $minutes."m".$seconds_left."s";
}


function getWinsSQLite($username, $dbType, $dbHandle) {
	$sql = "SELECT COUNT(*) FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND ((winner=1 AND dotaplayers.newcolour>=1 AND dotaplayers.newcolour<=5) OR (winner=2 AND dotaplayers.newcolour>=7 AND dotaplayers.newcolour<=11)) AND gameplayers.`left`/games.duration >= 0.8";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$inwins=$row["COUNT(*)"];
	}
	return $inwins;
}

function getLossesSQLite($username, $dbType, $dbHandle) {
	$sql = "SELECT COUNT(*) FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND ((winner=2 AND dotaplayers.newcolour>=1 AND dotaplayers.newcolour<=5) OR (winner=1 AND dotaplayers.newcolour>=7 AND dotaplayers.newcolour<=11)) AND gameplayers.`left`/games.duration >= 0.8";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$inlosses=$row["COUNT(*)"];
	}
	return $inlosses;
}

function getWinsMySQL($username) {

	$result = mysql_query("SELECT COUNT(*) FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND ((winner=1 AND dotaplayers.newcolour>=1 AND dotaplayers.newcolour<=5) OR (winner=2 AND dotaplayers.newcolour>=7 AND dotaplayers.newcolour<=11)) AND gameplayers.`left`/games.duration >= 0.8");
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$inwins=$row["COUNT(*)"];
	return $inwins;
}

function getLossesMySQL($username) {

	$result = mysql_query("SELECT COUNT(*) FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND ((winner=2 AND dotaplayers.newcolour>=1 AND dotaplayers.newcolour<=5) OR (winner=1 AND dotaplayers.newcolour>=7 AND dotaplayers.newcolour<=11)) AND gameplayers.`left`/games.duration >= 0.8");
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$inlosses=$row["COUNT(*)"];
	return $inlosses;
}

function checkIfAliasSQLite($value, $dbType, $dbHandle){

	$sql = "SELECT heroid, original FROM originals where heroid='$value'";
	$original="";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$original=$row["original"];
		$heroid=$row["heroid"];
	}
	
	//If this was an alias  we found the original
	if($original != "orig"){$value=$original;}
	//else we keep the original
	return $value;
}
function checkIfAliasMySQL($value){
	$sql = "SELECT heroid, original FROM originals where heroid='$value'";

	$result = mysql_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
	$original=$row["original"];
	$heroid=$row["heroid"];
	//If this was an alias  we found the original
	if($original != "orig"){$value=$original;}
	//else we keep the original
	
	return $value;
	print"value is $value";
}

function getTeam($color)
{
	switch ($color) {
		case 'red': return 0;
		case 'blue': return 1;
		case 'teal': return 1;
		case 'purple': return 1;
		case 'yellow': return 1;
		case 'orange': return 1;
		case 'green': return 0;
		case 'pink': return 2;
		case 'gray': return 2;
		case 'light-blue': return 2;
		case 'dark-green': return 2;
		case 'brown': return 2;
		case 'observer': return 0;
	}
}

function getRatio($nom, $denom) 
{
	if($nom == 0) {
		return 0;
	}
	else if($denom == 0) {
		return 1000;
	}
	else {
		return round((($nom*1.0)/($denom*1.0)),2);
	}
}

function getUserParam($username)
{
	if($username == '') 
	{
		return '';
	}
	else
	{
		return "&u=".$username;
	}
}

?>
