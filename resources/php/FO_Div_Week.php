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

//MySQL connection / Connexion à MySQL
//include('../../../config.php');
//GuildManager main configuration file / Fichier de configuration principal GuildManager
//include('../config.php');
//Language management / Gestion des traductions
//include('../language.php');

//Creating needed date variables / Création des variables de dates nécessaires
$date = strtotime($date);
$today = date('Y-m-d', $date );
$current_day = date('w', time() );
$current_week = date('W', time() );
$current_month = date('m', time() );
$current_year = date('Y', time() );

//day correction
$sql = "SELECT DATE_ADD('$today', INTERVAL - value DAY) FROM ".$gm_prefix."param  WHERE TYPE = 'day' AND complement=$current_day";
echo $sql;
$list=mysqli_query($con,$sql);
while($result=mysqli_fetch_row($list)) { $start_day = $result[0]; $start_day1 = $start_day;};

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
    echo "<script>function raidFormShow(){ $('#RaidForm').toggle('blind')}</script>";
?>