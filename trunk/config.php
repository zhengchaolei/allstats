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

/*************************************
 *	EDITABLE CONFIGURATION SETTINGS  *
 *************************************/

//Database type:
//Enter sqlite to use a SQLite database.
//Enter mysql to use a MySQL database.
//Configure your selected database below. 
$dbType = 'mysql';
 
//SQLite Database Connection information (Optional):
//dbLocation must point to your SQLite Database.
$dbLocation = 'THE_PATH_TO_YOUR_ghost.dbs';

//MySQL Database Connection information (Optional):
//Must correspond to the settings in your MySQL Database.
$host = 'localhost';
$username = 'YOUR_USERNAME';
$password = 'YOUR_PASSWORD';
$databasename = 'YOUR_GHOST_DATABASE';

//If you want  to download  the replay from the webpage you got to save the replay files in the replay folder
//This is easiest if you have your webpage on the same computer as you bot. However most people probably upload their page to an external site 
//this feature is disabled by default
$enablereplayfeature = false;  //  values:   true ,  false (default) 

//GHost++ bot user name:
$botName = 'YOUR_BOT_NAME';

//GHost++ root administrator name:
$rootAdmin = 'YOUR_ROOT_ADMIN';


//Settings for Top Players page

//Default minimum number of games played in order to be displayed on Top Players page:
$minGamesPlayed = 2;

//Pre-Calculate score
//If true:  Player scores will be taken from the score table in your MySQL database. You must populate this table through your own methods.
//		    One easy way to populate the score is to run the update_dota_elo.exe program in your GHost++ folder periodically. This will automatically populate
//          your scores table through an ELO method. Personally, I have modified my GHost++ to run update_dota_elo after every game automatically.
//If false: Player scores will be dynamically calculated on page load through a formula that takes into account kills, deaths, assists, wins, losses, etc...
//			This is less ideal and will slow your top players page load slightly. As of yet, I have not found a numeric scoring system that I believe 
//   		accurately reflects skill level.
$scoreFromDB = false;

//Score Formula: (Only used if $scoreFromDB = false)
//Must follow SQL formatting conventions.
//Allowed variables: totgames, kills, deaths, assists, creepkills, creepdenies, neutralkills, towerkills, raxkills, courierkills, wins, losses
//Backup of default formula in case of error: '((((kills-deaths+assists*0.5+towerkills*0.5+raxkills*0.2+(courierkills+creepdenies)*0.1+neutralkills*0.03+creepkills*0.03) * .2)+(wins-losses)))'
$scoreFormula = '((((kills-deaths+assists*0.5+towerkills*0.5+raxkills*0.2+(courierkills+creepdenies)*0.1+neutralkills*0.03+creepkills*0.03) * .2)+(wins-losses)))'; 
 
//Ignore public or private games on statistics pages
//Will only affect scores if you do not pre-calculate ($scoreFromDB = false).  
//If you do pre-calculate, you are expected to filter out public or private games on your own.
//IgnorePubs will override ignorePrivs if both are set to true.
$ignorePubs = false;
$ignorePrivs = false;

 
/**********************************
 *	DO NOT EDIT BELOW THIS POINT. *
 **********************************/ 
//SQLite
if($dbType == 'sqlite')
{
	try{

	$dbHandle = new PDO('sqlite:'.$dbLocation);

	}catch( PDOException $exception ){

	die($exception->getMessage());

	}
}
else
{ 
	//MySQL

	$link = mysql_connect($host,$username,$password);
	if (!$link) {
		die('Not connected : ' . mysql_error());
	}

	// make the current db
	$db_selected = mysql_select_db($databasename, $link);
	if (!$db_selected) {
		die ('Can\'t use current db : ' . mysql_error());
	}
}
?>