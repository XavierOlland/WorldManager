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
include('../../../config.php');
//GuildManager main configuration file / Fichier de configuration principal GuildManager
include('../config.php');
//Language management / Gestion des traductions
include('../language.php');

//Creating needed date variables / Création des variables de dates nécessaires
/*
$admin=$_GET['admin'];
$usertest = $_GET['user_ID'];
$date = $_GET['date'] ;
$date = strtotime($date);
$day = date('d', $date) ;
$month = date('m', $date) ;
$year = date('Y', $date) ;*/
$today = date('Y-m-d', time() );
$current_day = date('w', time() );
$current_week = date('W', time() );
$current_month = date('m', time() );
$current_year = date('Y', time() );

//day correction
$sql = "SELECT DATE_ADD('$today', INTERVAL - value DAY) FROM ".$gm_prefix."param  WHERE TYPE = 'day' AND complement=$current_day";
$list=mysqli_query($con,$sql);
while($result=mysqli_fetch_row($list)) { $start_day = $result[0]; };

echo "<h3>Semaine $current_week</h3>";
      //Day ordering / Ordre des jours

      for($day=0; $day<7; $day++ ){
      echo "<div class='day_container'"; if( strtotime($start_day) == strtotime($today) ) {echo " class='today_bg'";};
      echo "><a onclick=\"$('#Result').load('resources/php/FO_Div_Day.php?date=$start_day')\"><img class='full-cell' src='resources/theme/$theme/images/casper.png'></a>
      ";
       $sql = "SELECT d.$local
     FROM ".$gm_prefix."param AS p 
     LEFT JOIN ".$gm_prefix."dictionary AS d ON d.table_ID=p.param_ID AND d.entity_name='param' 
     WHERE TYPE = 'day' AND p.value=$day
     ORDER BY p.value";
      $list=mysqli_query($con,$sql);
      while($result=mysqli_fetch_row($list)) 
      {  echo "<div class='day_line'>".$result[0]."</div>
      <div class='day_line'>" ; };
      $counter_map = 1;
      $counter_strength = 0;
      $sql_strength = "SELECT IFNULL(MAX(a.strength),0) FROM (SELECT IFNULL(SUM(r.strength),0) AS strength FROM ".$gm_prefix."raid AS r GROUP BY param_ID_map) AS a";
      $list_strength=mysqli_query($con,$sql_strength);
      while($result_strength=mysqli_fetch_row($list_strength)){ $total_strength = $result_strength[0];};
      
      $sql_map = "SELECT p.param_ID, p.text_ID, 
                  (SELECT IFNULL(SUM(r.strength),0) FROM ".$gm_prefix."raid AS r WHERE r.param_ID_map = p.param_ID AND r.dateRaid = '$start_day') AS strength
                  FROM  ".$gm_prefix."param AS p 
                  WHERE p.type = 'map' 
                  ORDER BY param_ID";

      $list_map=mysqli_query($con,$sql_map);
      while($result_map=mysqli_fetch_array($list_map))
      {  if( $counter_map == 3 ) { echo "</div><div class='day_line'>"; }
        $size = round($result_map[strength]*50/$total_strength); if($size < 20 ){$size=20;};
         echo "<div class='day_cube'>
                    <div class='sq_".$result_map[text_ID]."' style='width:".$size."px;height:".$size."px'><div style='overflow: hidden;'>".$result_map[strength]."</div></div></div>" ; 
        $counter_map++;$counter_strength = $counter_strength +$result_map[strength];
     };
     $start_day1 = date_create($start_day);
     $start_day = date_format(date_add( $start_day1 , date_interval_create_from_date_string("1 day")), 'Y-m-d');
     echo "</div><div class='day_line'>Total: $counter_strength</div></div>";
     
    };
      
  echo "
    </tr>
  </tbody>
</table>";
?>