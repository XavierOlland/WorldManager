 <?php 
 /* Guild Manager v1.1.0 (Princesse d’Ampshere)
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
	
 //MySQL connection / Connexion à MySQL
include('../../../config.php');
//GuildManager main configuration file / Fichier de configuration principal GuildManager
include('../config.php');
//Language management / Gestion des traductions
include('../language.php');

//Page variables creation / Création des variables spécifiques pour la page
$usertest = $_GET['user'];
$id = $_GET['id'];
$date = $_GET['date'];
$action = $_GET['action'];
$character_ID = $_GET['character_ID'];
$title = strftime('%A %e %B', $date);
$title = utf8_encode( $title );
$day = date('l', $date);
$day = mysqli_fetch_row(mysqli_query($con,"SELECT CONCAT(type,'_',value) FROM ".$gm_prefix."param WHERE text_ID=LOWER('$day')"));
$sqlday= $day[0];
$sqldate = date('Y\-m\-d', $date);
$today = date('Y-m-d', time());

if( strlen($id) == 0 ){ 
	$sql="SELECT raid_event_id FROM ".$gm_prefix."raid_event WHERE dateEvent='$sqldate'";
	$list=mysqli_query($con,$sql);
	$result=mysqli_fetch_row($list);
	$id = $result[0]; if( strlen($id) == 0 ){ $id = 0; }; 
};

if ($id == 0){ echo "<script>$(\"#delete\").hide()</script>";} else {echo "<script >$(\"#delete\").show()</script>";};


//NEW
//Registering player
if ( $action == 0 || $action == 1 ){ 
	$sql = mysqli_query($con,"SELECT * FROM ".$gm_prefix."raid_player WHERE dateEvent='$sqldate' AND user_ID=$usertest");
	$count = mysqli_num_rows($sql);
	$list=mysqli_query($con,"SELECT $sqlday FROM ".$gm_prefix."userinfo WHERE user_ID=$usertest");
	while( $result=mysqli_fetch_row($list) ) { $day = $result[0]; };
	
		if( $count == 0 )
			{$sql1="INSERT INTO ".$gm_prefix."raid_player (user_ID, dateEvent, character_ID, presence ) VALUES ($usertest,'$sqldate',$character_ID,$action)"; }
		else { 
			if ( $action==0 && $day==0 ) { $sql1 = "DELETE FROM ".$gm_prefix."raid_player WHERE user_ID=$usertest AND dateEvent='$sqldate'" ;}
			else { $sql1 = "UPDATE ".$gm_prefix."raid_player SET character_ID='$character_ID', presence='$action' WHERE user_ID=$usertest AND dateEvent='$sqldate'";};
		};
	
	if (!mysqli_query($con,$sql1)){$actionresult=$lng[g__error_record]; }; 
	
	};
//END NEW

//Retrieving event information
$sql="SELECT r.raid_event_ID,  r.event, r.map, r.color, r.time, u.user_ID, r.comment
      FROM ".$gm_prefix."raid_event AS r 
      LEFT JOIN ".$table_prefix."users AS u ON u.user_ID=r.user_ID_leader
      WHERE r.raid_event_ID=$id";
 
$list=mysqli_query($con,$sql); 
while( $result=mysqli_fetch_row($list))
{
echo "<form name='RaidEvent' id='RaidEvent' method='POST' action='' onsubmit=\"return false\">
     <input type='hidden' name='raid_id' id='raid_id' value='".$id."'>
     <input type='text' id='dateEvent' class='h3' value='".$title."'/>
     <input type='hidden' name='dateEvent' id='hiddenDateEvent' value='".$sqldate."'><br />
     <input type='text' name='event' class='h4' value='".$result[1]."' /><br />
     <table>
		<tr><td colspan='2'><p>".$lng[t_raid_event_map]." : <input type='text' name='map' class='p' value='".$result[2]."'/>
		<tr><td colspan='2'><p>".$lng[t_raid_event_color]." : <select name='color' class='p'>
			<option value='#606060' ";  if ($result[3]=='#606060') { echo "selected" ;}; echo ">-</option>
			<option value='#A80000' style='color:#A80000;' ";  if ($result[3]=='#A80000') { echo "selected" ;}; echo ">".$lng[g__red]."</option>
			<option value='#0033FF' style='color:#0033FF;' ";  if ($result[3]=='#0033FF') { echo "selected" ;}; echo ">".$lng[g__blue]."</option>
			<option value='#006600' style='color:#006600;' ";  if ($result[3]=='#006600') { echo "selected" ;}; echo ">".$lng[g__green]."</option>
			<option value='#CC9933' style='color:#CC9933;' ";  if ($result[3]=='#CC9933') { echo "selected" ;}; echo ">".$lng[g__gold]."</option>
			</select></p></td></tr>
		<tr><td colspan='2'><p>".$lng[t_raid_event_time]." : <input type='text' name='time' class='p' value='".$result[4]."'/></p></td></tr>
		<tr><td colspan='2'><p>".$lng[t_raid_event_leader]." : <select name='user_ID_leader' class='p'>";
		$sqlC="SELECT u.username, u.user_ID FROM ".$table_prefix."users AS u LEFT JOIN ".$gm_prefix."userinfo AS i ON i.user_ID=u.user_ID WHERE i.commander=1";
		$listC=mysqli_query($con,$sqlC);
		while($resultC=mysqli_fetch_array($listC,MYSQLI_ASSOC))
		{ echo "<option value='".$resultC['user_ID']."' " ;if ($resultC['user_ID']==$result[5]) { echo "selected" ;}; echo ">".$resultC['username']."</option>";};
		echo "</select></p></td></tr>
		<tr class='top'><td><p>".$lng[t_raid_event_comment]." : </p></td><td>
		<textArea style='width:240px; height:80px;' form='RaidEvent' name='comment' >".$result[6]."</textArea></td></tr>
	 <tr><td colspan='2'><input type='submit' value='".$lng[g__save]."'></form></td></tr>
	 <tr><td colspan='2'><input type='button' id='delete' value='".$lng[g__delete]."' onclick=\"deleteEvent()\"></td></tr>
	 </table>";
//NEW	

if ($id != 0) {

	$sql0="
	SELECT x.user_ID, x.username,x.character_ID, x.name, x.param_ID_profession, x.text_ID, x.color, x.presence, x.partyorder
	FROM 
	(SELECT 
	u.user_ID, u.username,c.character_ID, c.name, c.param_ID_profession, p1.text_ID, p1.color, p2.partyorder, r.presence, CASE WHEN r.presence = 0 THEN 3 ELSE r.presence END AS crit1
	FROM ".$gm_prefix."userinfo AS u
	INNER JOIN ".$gm_prefix."raid_player AS r ON r.user_ID=u.user_ID 
	INNER JOIN ".$gm_prefix."character AS c ON c.character_ID=r.character_ID 
	INNER JOIN ".$gm_prefix."param AS p1 ON p1.param_ID=c.param_ID_profession
	INNER JOIN ".$gm_prefix."profession AS p2 ON p2.param_ID=p1.param_ID
	WHERE r.dateEvent='$sqldate'
	UNION
	SELECT 
	u.user_ID, u.username,c.character_ID, c.name, c.param_ID_profession, p1.text_ID, p1.color, p2.partyorder, 2 AS presence, 2 AS crit1
	FROM ".$gm_prefix."userinfo AS u
	INNER JOIN ".$gm_prefix."userinfo AS m ON m.user_ID=u.user_ID
	INNER JOIN ".$gm_prefix."character AS c ON c.user_ID=u.user_ID 
	INNER JOIN ".$gm_prefix."param AS p1 ON p1.param_ID=c.param_ID_profession
	INNER JOIN ".$gm_prefix."profession AS p2 ON p2.param_ID=p1.param_ID
	WHERE m.$sqlday=1 AND c.main=1) AS x
	GROUP BY x.user_ID
	ORDER BY x.crit1, x.partyorder, x.name";
	$list0=mysqli_query($con,$sql0);
	$count0=mysqli_num_rows($list0); 
	
	$sql1="SELECT DISTINCT(user_ID) FROM ".$gm_prefix."raid_player WHERE dateEvent='$sqldate' AND presence=1";
	$list1=mysqli_query($con,$sql1);
	$count1=mysqli_num_rows($list1);
	
	$sql2="SELECT DISTINCT(user_ID) FROM ".$gm_prefix."raid_player WHERE dateEvent='$sqldate' AND presence=0";
	$list2=mysqli_query($con,$sql2);
	$count2=mysqli_num_rows($list2);
	
	$count3=$count0-$count1-$count2;

	echo "

     <p style='font-weight:bold;'>$count1 ".$lng[p_FO_Div_Event_p_1]." ($count2 absents, $count3 &agrave; confirmer) <button onclick=\"playerlist()\" >Afficher / Masquer</button></p><br />
     <div id='BO_Member'>
	 <table>";
		
	while($result0=mysqli_fetch_array($list0,MYSQLI_ASSOC))
		{ 
		
		echo "<tr style='background-color:".$result0['color']."'>
		<td><a href='FO_Main_Profession.php?id=".$result0['param_ID_profession']."'><img src='resources/theme/$theme/images/".$result0['text_ID']."_Icon.png'></a></td>
		<td><select class='p' id='reroll".$result0['user_ID']."' onchange=\"eventPresence(1,".$result0['user_ID'].",this.value,reroll".$result0['user_ID'].")\">";
		
$sqlRR="SELECT character_ID, name FROM ".$gm_prefix."character WHERE user_ID=".$result0['user_ID'];
$listRR=mysqli_query($con,$sqlRR);
while($resultRR=mysqli_fetch_array($listRR,MYSQLI_ASSOC)) {
echo "<option value=\"".$resultRR['character_ID']."\" ";
if( $resultRR['character_ID'] == $result0['character_ID']){ echo "selected";};    

echo ">".$resultRR['name']."</option>" ;};


echo "</select></td>
		<td><a class='colorbg' href='FO_Main_User.php?user=".$result0['user_ID']."'>".$result0['username']."</a></td>";
		//Presence 

		if ($result0['presence'] == 0) { 
			echo "<td class='center'>Absent <a class='menu' onclick=\"eventPresence(1,".$result0['user_ID'].",".$result0['character_ID'].")\" href=\"javascript:void(0)\">(changer)</a></td>"; };
		if ($result0['presence'] == 1) { 
			echo "<td class='center'>Pr&eacute;sent <a class='menu' onclick=\"eventPresence(0,".$result0['user_ID'].",".$result0['character_ID'].")\" href=\"javascript:void(0)\">(changer)</a></td>"; };
		if ($result0['presence'] == 2) { 
			echo "<td><a class='menu' onclick=\"eventPresence(1,".$result0['user_ID'].",".$result0['character_ID'].")\" href=\"javascript:void(0)\">Confirmer</a> / <a class='menu' onclick=\"eventPresence(0,".$result0['user_ID'].",".$result0['character_ID'].")\" href=\"javascript:void(0)\">Absent</a></td>";};
			//End Presence

	};
echo "
</tr></table></div>
<br />

<form name='Add' id='Add' action='' method='post' onsubmit=\"return false\">
<fieldset>
<legend class='admin'>".$lng['g__addPlayer']."</legend>
<select class='p' id='usercharacter' name='usercharacter'>
";

$sql4="SELECT 
	u.user_ID, u.username,c.character_ID, c.name, c.param_ID_profession, p1.text_ID, p1.color, p2.partyorder
	FROM ".$gm_prefix."userinfo AS u
	INNER JOIN ".$gm_prefix."userinfo AS m ON m.user_ID=u.user_ID
	INNER JOIN ".$gm_prefix."character AS c ON c.user_ID=u.user_ID 
	INNER JOIN ".$gm_prefix."param AS p1 ON p1.param_ID=c.param_ID_profession
	INNER JOIN ".$gm_prefix."profession AS p2 ON p2.param_ID=p1.param_ID
	WHERE m.$sqlday=0 AND c.main=1
	GROUP BY u.user_ID
	ORDER BY p2.partyorder, c.name";
	$list4=mysqli_query($con,$sql4);
	while($result4=mysqli_fetch_array($list4,MYSQLI_ASSOC))
		{ echo "<option value='&user=".$result4[user_ID]."&character_ID=".$result4[character_ID]."'>".$result4[name]." (".$result4[username].")</option>"; };

echo "</select>
<input type='submit' value='".$lng['g__add']."' />
</fieldset>
</form>";
	
	};

//END NEW

echo "
<script src='resources/style/jquery.min.js'></script> 
<script src='resources/style/jquery-ui.js'></script>	 
<script>
$('#RaidEvent').submit(function(){   
		$.ajax({
			type: \"POST\",
			url: \"resources/php/BO_Script_Event.php\",
			data: $('#RaidEvent').serialize() + \"&date=$date&id=$id&action=update\",
			success: function(){
				$(\"#BO_Calendar\").load(\"resources/php/BO_Div_Calendar.php?date=$today\");
				$(\"#BO_Event\").load(\"resources/php/BO_Div_Event.php?date=$date\");
			}
		});
});
</script>";
	
//DELETE
echo "<script>
function deleteEvent(){   
		 $.post(\"resources/php/BO_Script_Event.php\",{id:$id,action:'delete'},function(){
				$(\"#BO_Calendar\").load(\"resources/php/BO_Div_Calendar.php?date=$today\");
				$(\"#BO_Event\").load(\"resources/php/BO_Div_Event.php?date=$date&id=0\");
			});
			}</script>";

//Date picker
		echo "<script> $(function() { $( \"#dateEvent\" ).datepicker({ 
dateFormat: \"DD d MM\",  altField: \"#hiddenDateEvent\",
altFormat: \"yy-mm-dd\" }); });</script>
<script>
	function eventPresence(a,b,c,d){  
		$.ajax({
			type: \"GET\",
			url: \"resources/php/BO_Div_Event.php?admin=$admin&id=$id&date=$date&action=\" + a + \"&user=\" + b + \"&character_ID=\" + c,
			data: '',
			success: function(html){ $('#BO_Event').html(html);}
		});	
	}	
</script>
<script>
	$('#Add').submit(function(){ 
		var a = $('#usercharacter').val();
		
		$.ajax({
			type: \"GET\",
			url: \"resources/php/BO_Div_Event.php?admin=$admin&id=$id&date=$date&action=1\" + a ,
			data: '',
			success: function(html){ $('#BO_Event').html(html);}
		});	
	});	
</script>
<script>
function playerlist(){
console.log('enter');
	if ( $(\"#BO_Member\").is(\":hidden\") === true ) { $(\"#BO_Member\").show(); console.log('show');} 
	else { $(\"#BO_Member\").hide(); console.log('hide');}
}
</script>

";
};

 ?>