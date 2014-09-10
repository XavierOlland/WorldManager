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

//PHPBB connection / Connexion à phpBB
include('resources/phpBB_Connect.php');
//GuildManager main configuration file / Fichier de configuration principal GuildManager
include('resources/config.php');
//Language management / Gestion des traductions
include('resources/language.php');

$user_id = $user->data['user_id'];

$sql_user = "SELECT i.guild_ID, i.herald 
     FROM ".$gm_prefix."userinfo AS i 
     WHERE i.user_ID=$user_id";
$list_user=mysqli_query($con,$sql_user);
while($result_user=mysqli_fetch_row($list_user)) { 
    $guild_id = $result_user[0]; 
    $herald = $result_user[1]; 
};
$date = $_GET['date'];
$date = strtotime($date);
$today = date('Y-m-d', $date );
$current_day = date('w', time() );
$current_week = date('W', time() );
$current_month = date('m', time() );
$current_year = date('Y', time() );

//day correction
$sql = "SELECT DATE_ADD('$today', INTERVAL - value DAY) FROM ".$gm_prefix."param  WHERE TYPE = 'day' AND complement=$current_day";
$list=mysqli_query($con,$sql);
while($result=mysqli_fetch_row($list)) { $start_day = $result[0]; $start_day1 = $start_day;};
$day = date('Y-m-d', strtotime( $start_day ) );
//Start of html page / Début du code html
echo	"<html>
<head>";
	include('resources/php/FO_Head.php');
	echo "
</head>
<body>
	<div id='Main'>
		<div id='Title'><h1>$cfg_title</h1></div>";
		//User permissions test / Test des permissions utilisateur
		if (in_array($user->data['group_id'],$cfg_groups)){
		//Registered user code / Code pour utilisateurs enregistrés
		echo "
		<div id='Left'>";
			include('resources/php/FO_Div_Menu.php');
			include('resources/php/FO_Div_Match.php');		
			echo "
		</div>";
		echo "
		<div id='Page'> 
			<div id='CoreFull'>
				<div class='Extand' id='Week'>";


//Week display
echo "<h3>Semaine $current_week</h3>";
      //Day ordering / Ordre des jours
      for($day_count=0; $day_count<7; $day_count++ ){
      echo "<div class='week_day_container"; 
      if ( $day_count & 1 ) { echo " oddday_bg";}; 
      if( strtotime($start_day1) === strtotime($today) ) {echo " today_bg";};
      echo "'><a onclick=\"$('#Result').load('resources/php/FO_Div_Day.php?date=$start_day1')\"><img class='full-cell' src='resources/theme/$theme/images/casper.png'></a>
      ";
       $sql = "SELECT d.$local
     FROM ".$gm_prefix."param AS p 
     LEFT JOIN ".$gm_prefix."dictionary AS d ON d.table_ID=p.param_ID AND d.entity_name='param' 
     WHERE TYPE = 'day' AND p.value=$day_count
     ORDER BY p.value";
      $list=mysqli_query($con,$sql);
      while($result=mysqli_fetch_row($list)) 
      { $num_day = date('d', strtotime($start_day1) );
        echo "<div class='week_day_line'>".$result[0]." $num_day</div>
      <div class='week_day_maps_line'>" ; };
      $counter_map = 1;
      $counter_strength = 0;
      $sql_strength = "SELECT IFNULL(MAX(a.strength),0) FROM (SELECT IFNULL(SUM(r.strength),0) AS strength FROM ".$gm_prefix."raid AS r GROUP BY param_ID_map) AS a";
      $list_strength = mysqli_query($con,$sql_strength);
      while($result_strength=mysqli_fetch_row($list_strength)){ $total_strength = $result_strength[0];};
      
      $sql_map = "SELECT p.param_ID, p.text_ID, 
                  (SELECT IFNULL(SUM(r.strength),0) FROM ".$gm_prefix."raid AS r WHERE r.param_ID_map = p.param_ID AND r.dateRaid = '$start_day1') AS strength
                  FROM  ".$gm_prefix."param AS p 
                  WHERE p.type = 'map' 
                  ORDER BY param_ID";

      $list_map=mysqli_query($con,$sql_map);
      while($result_map=mysqli_fetch_array($list_map))
      {  if( $counter_map == 3 ) { echo "</div><div class='week_day_maps_line'>"; }
        $size = round($result_map[strength]*50/$total_strength); 
        if($size < 20 ){$size=20;};
        
        echo "<div class='week_day_map_square_container'>
                <div class='week_day_map_square'>
                  <div class='week_day_map_container' style='width:".$cubepad."px;height:".$cubepad."px;'>
                    <div class='sq_".$result_map[text_ID]."' style='width:".$size."px;height:".$size."px'>".$result_map[strength]."</div>
                  </div>
                </div>
              </div>" ; 
        $counter_map++;$counter_strength = $counter_strength +$result_map[strength];
     };
     $start_day1 = date_create($start_day1);
     $start_day1 = date_format(date_add( $start_day1 , date_interval_create_from_date_string("1 day")), 'Y-m-d');
     echo "</div><div class='week_day_line'>Total: $counter_strength</div></div>";
     
    };
      
  echo "<div class='week_bottom_line' style='width:605px;'><p><img src='resources/theme/$theme/images/upperReturn.png'> Cliquez sur un jour pour en voir le détail.</p></div>";
  if ( $herald == 1 ) { 
    echo "<div id='adminLink'  class='week_bottom_line' style='width:200px;text-align:right'><a class='menu' href=\"javascript:void(0)\" onclick=\"raidFormShow()\">".$lng['g__adminPanel']."</a></div>";
  }
  else {
    echo "<div id='adminLink'  class='week_bottom_line' style='width:200px;text-align:right'></div>";
  };

					
				echo "</div>
				<div class='Extand' id='RaidForm' hidden>";


//Forms
echo "<form name='raids' id='raids' method='POST' action=''>";
for($day_count=0; $day_count<7; $day_count++ ){
  echo "<div class='week_day_container";
  if ( $day_count & 1 ) { echo " oddday_bg";};
  if( strtotime($day) === strtotime($today) ) {echo " today_bg";};
  echo "'>";
  $sql_raid = "SELECT IFNULL(r.strength,0) AS test, r.strength,
     IFNULL(DATE_FORMAT(r.startRaid,'%H:%i'),'20:30') AS startRaid,
     IFNULL(DATE_FORMAT(r.endRaid,'%H:%i'),'23:30') AS endRaid,
     r.param_ID_map AS guild_map
     FROM ".$gm_prefix."guild AS g 
     LEFT JOIN ".$gm_prefix."raid AS r ON r.guild_ID=g.guild_ID AND r.dateRaid='$day'
     WHERE g.guild_ID=$guild_id";

  $list_raid=mysqli_query($con,$sql_raid);
  while($result_raid=mysqli_fetch_array($list_raid)) {
    echo "    
    <div class='week_day_form'>
      <input type='hidden' name='id[]' value='$day' />
      <input type='hidden' name='test$day' value='".$result_raid[test]."' />
      <p>
        <select class='week_day_raid_form' name='map$day'>";
        $sql_map = "SELECT d.$local AS map, p.text_ID, p.param_ID, p.color,p.value
          FROM ".$gm_prefix."param AS p 
          LEFT JOIN ".$gm_prefix."dictionary AS d ON d.table_ID=p.param_ID AND d.entity_name='param' 
          WHERE TYPE = 'map' ORDER BY p.param_ID";
        $list_map=mysqli_query($con,$sql_map);
        while($result_map=mysqli_fetch_array($list_map)){ 
          echo "<option style='color:".$result_map[color]."' value='".$result_map[param_ID]."'";
          if ( $result_map[param_ID] == $result_raid[guild_map] ) { echo "selected"; }
          echo ">".$result_map[value]."</option>";
        };
        echo "</select></p><p style='text-align:right'>
        Effectif : <input class='week_day_raid_form number' name='strength$day' type='text' min='1' value='".$result_raid[strength]."'><br/>
        De : <input class='week_day_raid_form time' name='startRaid$day' type='text' value='".$result_raid[startRaid]."'><br/>
        à : <input class='week_day_raid_form time' name='endRaid$day' type='text' value='".$result_raid[endRaid]."'><br/><br/>
        </p>
        <p><input type='checkbox' name='check$day' checked>MàJ</p> 
        <p><input type='checkbox' name='delete$day'>Suppr.</p>     
    </div>" ; 
  };  
  echo "</div>";
  $day = date_create($day);
  $day = date_format(date_add( $day , date_interval_create_from_date_string("1 day")), 'Y-m-d');   
};		echo "
  <p style='margin:10px 0 20px 0;background-color:rgba(50, 55, 55, 0.15);text-align:right;'>
  <input type='submit' name='submit_val' value='Mettre à jour'> les raids sélectionnés </p>
  </form>";
				echo "</div>
				<div class='Extand' id='Result'></div>
			</div>
			<div id='Copyright'>".$lng[g__copyright]."</div>
		</div>
	</div>
	<script>function raidFormShow(){ $('#RaidForm').toggle('blind')}</script>
	<script>$(function(){ $('.time').mask('00:00');$('.number').mask('099');})</script>
	<script>var api_lng = '$api_lng'; var default_world_id = $api_srv</script>
	<script src=\"resources/js/Menu_Match.js\"></script>  
</body>
</html>"; 
if (isset($_POST['submit_val'])) {
  foreach($_POST['id'] as $id)
  { $endDate =$id.' '.$_POST['endRaid'.$id];
$startDate =$id.' '.$_POST['startRaid'.$id];
    $endDate=date_create($endDate);
    $startDate=date_create($startDate);
    if( $endDate < $startDate) {
      echo $_POST['endDate'.$id];
      $endDate = date_create($id);
      $endDate = date_format(date_add( $endDate, date_interval_create_from_date_string("1 day")), 'Y-m-d'); 
    }
    else {
    $endDate = $id; echo $_POST['endDate'.$id];
          }
    if( ($_POST['check'.$id] == 'on' && $_POST['strength'.$id] > 0) || ($_POST['delete'.$id] == 'off')) {
      if( $_POST['test'.$id] > 0) {
        $sql1 = "UPDATE wm_raid SET  param_ID_map=".$_POST['map'.$id].", strength = '".$_POST['strength'.$id]."', startRaid = '$id ".$_POST['startRaid'.$id]."', endRaid = '$endDate ".$_POST['endRaid'.$id]."' WHERE dateRaid='$id' AND guild_ID=$guild_id";
      }
      else {
        $sql1 = "INSERT INTO wm_raid (guild_ID,dateRaid,param_ID_map,strength,startRaid,endRaid) VALUES ($guild_id, '$id', ".$_POST['map'.$id].",'".$_POST['strength'.$id]."','$id ".$_POST['startRaid'.$id]."', '$endDate ".$_POST['endRaid'.$id]."')";
      }
      if( $_POST['delete'.$id] == 'on') {
        $sql1 = "DELETE FROM wm_raid  WHERE dateRaid='$id' AND guild_ID=$guild_id";
      }      
      mysqli_query($con,$sql1);
echo "<script>
        window.opener.location.reload();
</script>";
    }  
  }

}
}
//Non authorized user / utilisateur non autorisé
else { include('resources/php/FO_Div_Register.php'); }
?>
