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
$usertest = $_GET['user_ID'];*/
$date = $_GET['date'];
$unix_date = strtotime($date);
$title = strftime('%A %e %B', $unix_date);
$title = utf8_encode( $title );
$today = date('Y-m-d', time() );
$computed_day = date('w', time() );
$current_week = date('W', time() );
$current_day = date('d', time() );
$current_month = date('m', time() );
$current_year = date('Y', time() );


echo "<h3>$title</h3>" ;

//maps
$sql_map = "SELECT d.$local AS map, p.text_ID, p.param_ID, p.color
     FROM ".$gm_prefix."param AS p 
     LEFT JOIN ".$gm_prefix."dictionary AS d ON d.table_ID=p.param_ID AND d.entity_name='param' 
     WHERE TYPE = 'map' ORDER BY p.param_ID";
      $list_map=mysqli_query($con,$sql_map);
      while($result_map=mysqli_fetch_array($list_map))
      {  echo "<div class='map_day_title' style='float: left;height:26px;color:".$result_map[color]."'>
            <div style='float:left;width:390px;height:22px;margin:0;padding:4px 0 0px 5px;'>".$result_map[map]."</div>
            <div style='float:left;width:60px;height:22px;margin:0;padding:4px 0 0px 0;'>19h</div>
            <div style='float:left;width:60px;height:22px;margin:0;padding:4px 0 0px 0;'></div>
            <div style='float:left;width:60px;height:22px;margin:0;padding:4px 0 0px 0;'>21h</div>
            <div style='float:left;width:60px;height:22px;margin:0;padding:4px 0 0px 0;'></div>
            <div style='float:left;width:60px;height:22px;margin:0;padding:4px 0 0px 0;'>23h</div>
            <div style='float:left;width:60px;height:22px;margin:0;padding:4px 0 0px 0;'></div>
            <div style='float:left;width:60px;height:22px;margin:0;padding:4px 0 0px 0;'>01h</div>
        </div></br>
        ";
        $counter_strength = 0;
        //raids
        $sql_raid = "SELECT g.guild_ID, g.tag, g.name, r.strength, r.type, DATE_FORMAT( r.startRaid, '%Hh%i') AS startRaid, DATE_FORMAT(r.endRaid, '%Hh%i') AS endRaid, 
        ROUND(time_to_sec(timeDIFF(endRaid, startRaid))/60,0) AS length,
        ROUND(time_to_sec(timeDIFF(startRaid, '$date 19:00:00'))/60+15,0) AS startPadding
        FROM ".$gm_prefix."raid AS r 
        LEFT JOIN ".$gm_prefix."guild AS g ON g.guild_ID=r.guild_ID
        WHERE r.param_ID_map=".$result_map[param_ID]." AND dateRaid = '$date'";
        $list_raid=mysqli_query($con,$sql_raid);
        while($result_raid=mysqli_fetch_array($list_raid))
        {   
            echo "<div style='float: left;padding:3px 0 1px 10px;'>
                    <div  style='float: left;width:20px;height:20px;margin:0;padding:0;'><img src='resources/theme/$theme/images/".$result_raid[type]."_Icon.png'></div>
                    <div  style='float: left;width:60px;height:20px;margin:0;padding:0;text-align:center;'><a class='table' href='FO_Main_Guild?guild_ID=".$result_raid[guild_ID]."'>".$result_raid[tag]."</a></div>
                    <div  style='float: left;width:275px;height:20px;margin:0;padding:0;'>".$result_raid[name]."</div>
                    <div  style='float: left;width:25px;height:20px;margin:0;padding:0;text-align:right;'>".$result_raid[strength]."</div>
                    <div  style='float: left;width:420px;height:20px;'>
                        <div class='".$result_map[text_ID]."' style='width:".$result_raid[length]."px;margin-left:".$result_raid[startPadding]."px'>&nbsp;</div>
                    </div>
                </div></br>"; 
         $counter_strength = $counter_strength + $result_raid[strength]; } ;
         echo "<div style='float: left;margin:0px 0 15px 10px;background-color:rgba(50, 55, 55, 0.15);'>
                    <div  style='float: left;width:60px;height:20px;margin:0;padding:0;'></div>
                    <div  style='float: left;width:295px;height:20px;margin:0;padding:0;text-align:right;'>Total : </div>
                    <div  style='float: left;width:25px;height:20px;margin:0;padding:0;text-align:right;'>$counter_strength</div>
                    <div  style='float: left;width:420px;height:20px;'></div>
                </div>" ; 
         };
     
?>