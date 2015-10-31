<?php
/*  Guild Manager v0.1.0 ()
	Guild Manager has been designed to help Guild Wars 2 (and other MMOs) guilds to organize themselves for PvP battles.
    Copyright (C) 2013  Xavier Olland

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. */

//PHPBB connection / Connexion � phpBB
include('resources/phpBB_Connect.php');
//GuildManager main configuration file / Fichier de configuration principal GuildManager
include('resources/config.php');
//Language management / Gestion des traductions
include('resources/language.php');

//Page variables creation / Cr�ation des variables sp�cifiques pour la page
$id = $_GET['user'];
$action = $_GET['action'];
if (in_array($user->data['group_id'],$cfg_groups_backoffice)){ $admin = 1; };

//Start of html page / D�but du code html
echo "
<html>
<head>";
//Common <head> elements / El�ments <head> communs
	include('resources/php/FO_Head.php');

//Page specific <head> elements / El�ments <head> sp�cifique � la page
//Scripts
echo "</head>
<body>
	<div id='Main'>
		<div id='Title'><h1>".$cfg_title."</h1></div>";
//User permissions test / Test des permissions utilisateur
			if (in_array($user->data['group_id'],$cfg_groups)){
			//Registered user code / Code pour utilisateurs enregistr�s
		echo "
		<div id='Left'>";
			include('resources/php/FO_Div_Menu.php');
			include('resources/php/FO_Div_Match.php');
		echo "
		</div>";
		echo "
		<div id='Page'>
			<div id='CoreFull'>";
					$sqlg="SELECT name, tag, strength
					FROM ".$gm_prefix."guild AS g
					WHERE guild_ID = $guild_id" ; 
					$listg=mysqli_query($con,$sqlg);
					while($resultg=mysqli_fetch_array($listg,MYSQLI_ASSOC))
					{ echo "
				<h2>".$resultg['name']."</h2><br/>
				".$lng[t_guild_name]." : <input class='guild_form' type='text' value=\"".$resultg['name']."\" /><br />
				".$lng[t_guild_tag]." : <input class='guild_form' type='text' value=\"".$resultg['tag']."\" /><br />
				".$lng[t_guild_strength]." : <input class='guild_form' type='number' value=\"".$resultg['strength']."\" /><br />
				".$lng[t_guild_strengthText]." : <input class='guild_form' type='text' value=\"".$resultg['strengthText']."\" /><br />";
					};
					echo "</tbody>
					</table>
				<br />
				<br />
				<div class='extand' id='Result'></div>
			</div>
		</div>
		<div id='Copyright'>".$lng[g__copyright]."</div>
	</div>
	<script>var api_lng = '$api_lng'; var default_world_id = $api_srv</script>
	<script src=\"resources/js/Menu_Match.js\"></script>
</body>
</html>"; }
//Non authorized user / utilisateur non autoris�
else { include('resources/php/FO_Div_Register.php'); }
?>

