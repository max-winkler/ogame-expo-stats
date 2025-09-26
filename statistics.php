<?php
require_once("include/page.php");
require_once("include/expo_functions.php");

load_page(__FILE__, "statistics");

$db = connect_db();

$colors = array('#e6194b', '#3cb44b', '#4363d8', '#f58231', '#911eb4', '#46f0f0', '#f032e6', '#bcf60c', '#fabebe', '#008080', '#e6beff', '#9a6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#808080', '#000000', '#f5bec8');
$users = get_all_users($db);

// Get user Id from url or cookie
if(isset($_GET['user']))
    $userId = $_GET['user'];
else
    $userId = NULL;

if(isset($_GET['time_key']))
    $time_key = $_GET['time_key'];
else
    $time_key = NULL;
  
if(strcmp($userId,"all") == 0)
    $userId = NULL;
if(strcmp($time_key, "alltime") == 0)
    $time_key = NULL;
?>

<h1>Statistik</h1>

<div style="overflow:hidden;">
  <div style="margin-bottom: 20px; margin-right: 50px; float: left;">
    <b>Nutzer:</b>
    <select id="user" onchange="refresh()">
      <option value="all">Alle</option>
<?php
foreach($users as $user)
{
    $select = "";
    if($user['Id'] == $userId)
        $select = " selected";
    
    echo "    <option value=\"".$user['Id']."\"$select>".$user['Name']."</option>\n";
}
?>        
    </select>
  </div>
  <div style="margin-bottom: 20px; float: left;">
    <b>Zeitraum:</b>
    <select id="time_key" onchange="refresh()">
      <option value="alltime" <?php if(strcmp($time_key,"alltime")==0) echo "selected=\"selected\"";?>>Allzeit</option>
      <option value="today" <?php if(strcmp($time_key,"today")==0) echo "selected=\"selected\"";?>>Heute</option>
      <option value="yesterday" <?php if(strcmp($time_key,"yesterday")==0) echo "selected=\"selected\"";?>>Gestern</option>
      <option value="this_week" <?php if(strcmp($time_key,"this_week")==0) echo "selected=\"selected\"";?> >Diese Woche</option>
      <option value="last_week" <?php if(strcmp($time_key,"last_week")==0) echo "selected=\"selected\"";?>>Letzte Woche</option>
      <option value="this_month" <?php if(strcmp($time_key,"this_month")==0) echo "selected=\"selected\"";?>>Dieser Monat</option>
      <option value="last_month" <?php if(strcmp($time_key,"last_month")==0) echo "selected=\"selected\"";?>>Letzter Monat</option>
    </select>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
function refresh()
{
    console.log("Refreshing");
    
    var userId = $('#user').val();
    var time_key = $('#time_key').val();

    var args = [];

    if(userId != 'all')
        args.push("user=" + userId);

    if(time_key != 'alltime')
        args.push("time_key=" + time_key);
    
    var url = window.location.href.split('?')[0];
    if(args.length > 0)
        url += '?' + args.join('&');

    console.log(url);
    window.location=url;
}
</script>

<h2>Häufigkeit der Expeditionsereignisse</h2>

<?php
$statistics = get_expo_statistics($db, $userId, $time_key);
?>

<table>
  <thead>
    <tr>
      <th>Geflogene Expeditionen</th>
      <th>
<?php
$nr_expos = $statistics['NrExpeditions'];
echo $nr_expos;
?>
      </th>
      <th>100%</th>
    </tr>
  </thead>
  <tbody>
<?php

global $expo_types;

foreach($statistics as $expo_key=>$count)
{
    if(!array_key_exists($expo_key, $expo_types))
        continue;
    
    $probability = $count/$nr_expos*100;
    $probability = number_format($probability, 2, ",", ".")."%";
    
    echo "<tr>\n";
    echo "<td>".$expo_types[$expo_key]."</td>\n";
    echo "<td>".$count."</td>\n";
    echo "<td>".$probability."</td>\n";
    echo "</tr>";
}
?>
  </tbody>
</table>

<div style="overflow:hidden;">
<div style="margin-bottom: 20px; margin-right: 50px; float: left;">
<h2>Ressourcenfunde gesamt</h2>
<table>
  <thead>
    <tr>
      <th style="width: 300px;">Ressource</th>
      <th>Menge</th>
    </tr>
  </thead>
  <tbody>
<?php
global $resource_names;

foreach($resource_names as $res_type=>$res_name)
{
    echo "<tr>\n";
    echo "<td>$res_name</td>\n";
    echo "<td>".to_formatted($statistics[$res_type])."</td>\n";
    echo "</tr>\n";
}
?>
  </tbody>
</table>
</div>
<div style="margin-bottom: 20px; float: left;">
<h2>Flottenfunde gesamt</h2>
<table>
  <thead>
    <tr>
      <th style="width: 300px;">Schiff</th>
      <th>Menge</th>
    </tr>
  </thead>
  <tbody>
<?php
global $ship_names;

foreach($ship_names as $ship_type=>$ship_details)
{
    if(empty($statistics[$ship_type]))
        continue;
    
    echo "<tr>\n";
    echo "<td>".$ship_details['name']."</td>\n";
    echo "<td>".to_formatted($statistics[$ship_type])."</td>\n";
    echo "</tr>\n";
}
?>
  </tbody>
</table>
</div>
</div>
<div>

<h2>LF-Performance</h2>

Folgende Tabelle fässt die Ressourcenfunde der letzten 30 Tage zusammen.
                   
<?php
$mse_stats = get_mse_stats($db);

                   usort($mse_stats, function($a, $b)
                         {
                             return $a['ResValue']/$a['NrExpos'] < $b['ResValue']/$b['NrExpos'];
                         }
                   );

?>
  
<table>
  <thead>
    <tr>
      <th>Platz</th>
      <th>User</th>
      <th>Ressourcenfunde</th>
      <th>Ressourcen (MSE)</th>
      <th>MSE/Expo</th>
    </tr>
  </thead>
  <tbody>
    <?php
$i = 0;                   
foreach($mse_stats as $stat)
{
    $i = $i+1;
    echo "<tr>\n";
    echo "  <td>".$i."</td>\n";
    echo "  <td><span class=\"user_name\">".$stat['Name']."</span></td>\n";
    echo "  <td>".$stat['NrExpos']."</td>\n";
    echo "  <td>".to_formatted($stat['ResValue'])."</td>\n";
    echo "  <td>".to_formatted($stat['ResValue']/$stat['NrExpos'])."</td>\n";
    echo "</tr>\n";
}
    ?>
  </tbody>
</table>                 
                   
<h2>Nutzerstatistiken</h2>
<script>
window.onload = function () {

var options = {
	animationEnabled: true,
	theme: "light2",
	axisX:{
		valueFormatString: "DD MMM"
	},
	axisY: {
		title: "Ressourcenfunde",
		suffix: " Mrd",
	        labelFormatter: function(e) {
        	    return Math.floor(e.value/1000);
        	}
	},
	toolTip:{
		shared:true
	},  
	legend:{
		cursor:"pointer",
		verticalAlign: "bottom",
		horizontalAlign: "center",
		itemclick: toogleDataSeries
	},
	data: [
<?php
foreach($users as $i=>$user)
{
    $stats = get_user_statistics($db, $user['Id']);

    echo "{\n";
    echo "type: \"spline\",\n";
    echo "showInLegend: true,\n";
    echo "name: \"".$user['Name']."\",\n";
    echo "markerType: \"square\",\n";
    echo "xValueFormatString: \"DD MMM\",\n";
    echo "color: \"".$colors[$i]."\",\n";
    echo "yValueFormatString: \"#,##0M\",\n";
    echo "dataPoints: [\n";
    
    foreach($stats as $stat)
    {
    $value = $stat['ResValue'];
    $value/= 1000;
    $value/= 1000;
    echo "{ x: new Date('".$stat['Date']."'), y: ".$value." },\n";
    }
    echo "]\n";
    echo "},\n";
}
?>
	]
};

$("#chartContainer").CanvasJSChart(options);

function toogleDataSeries(e){
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	} else{
		e.dataSeries.visible = true;
	}
	e.chart.render();
}

}
</script>
<div align="center">
<div id="chartContainer" style="height: 500px; width: 95%;"></div>
</div>
<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
</div>

