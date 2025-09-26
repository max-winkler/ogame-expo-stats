<?php
require_once("include/page.php");
require_once("include/expo_functions.php");

load_page(__FILE__, "overview");

$db = connect_db();
if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $date = new DateTime($date);
}
else
    $date = new DateTime('NOW');

// Get midnight
$date->setTime(0,0);
$next_date = clone $date;
$next_date->add(new DateInterval("P1D"));

// Get user Id from url or cookie
if(isset($_GET['user']))
    $userId = $_GET['user'];
else
    $userId = NULL;

if(strcmp($userId,"all")==0)
    $userId = NULL;

$expos = get_expos($db, $date, $next_date, $userId);

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
function refresh(increment=0)
{
    // Set semester and type filter in URL
    var userId = $('#user').val();
    var args = "?user=" + userId;

    if(increment != 0)
    {
        var date_str = $('#date').val();
        var parts = date_str.match(/(\d+)/g);
        var date = new Date(parts[2], parts[1]-1, parts[0]);
        date.setDate(date.getDate() + increment);

        args += '&date='+date.toDateString().replaceAll(' ','+');
    }
    
    var url = window.location.href.split('?')[0] + args;

    window.location=url;
}
</script>
    
<h1>Meine Expeditionen</h1>
<div style="overflow:hidden;">
  <div style="margin-bottom: 20px; margin-right: 20px; float: left;">
    <b>Nutzer:</b>
    <select id="user" onchange="refresh()">

<?php
  $users = get_all_users($db);
  echo "<option value=\"all\">Alle</option>\n";
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
    <b>Datum:</b>
    <input id="date" value="<?php echo $date->format("d.m.Y");?>" readonly></input>
    <a href="#" onclick="refresh(-1)">früher</a> | <a href="#"
					    onclick="refresh(1)">später</a>
  </div>
</div>
<h2>Zusammenfassung</h2>

<?php
    
$metall = 0;
$crystal = 0;
$deuterium = 0;
$darkmatter = 0;
$fleet = array();

global $ship_names;

foreach($ship_names as $ship=>$ship_detail)
    $fleet[$ship] = 0;

$nr_res_found = 0;
$nr_dm_found = 0;
$nr_fleet_found = 0;
$nr_pirate_attacks = 0;
$nr_alien_attacks = 0;
$nr_fleet_lost = 0;
$nr_trader_found = 0;

foreach($expos as $expo)
{
    switch($expo['Type'])
    {
    case "R":
        $nr_res_found++;
        if(strcmp($expo['ResType'], "metall")==0)
            $metall = $metall + $expo['ResAmount'];                
        elseif(strcmp($expo['ResType'], "crystal")==0)
            $crystal = $crystal + $expo['ResAmount'];
        elseif(strcmp($expo['ResType'], "deuterium")==0)
            $deuterium = $deuterium + $expo['ResAmount'];
        break;
    case "D":
        $nr_dm_found++;
        $darkmatter = $darkmatter + $expo['ResAmount'];
        break;
    case "F":
        $nr_fleet_found++;
        $fleet_found = $expo['Fleet'];
        $fleet_found = explode(",", $fleet_found);

        foreach($fleet_found as $ship_details)
        {
            $ship_details = explode(": ", $ship_details);
            $ship_type = $ship_details[0];
            $ship_amount = $ship_details[1];

            $fleet[$ship_type] = $fleet[$ship_type] + $ship_amount;
        }
        break;
    case "P":
        $nr_pirate_attacks++;
        break;
    case "A":
        $nr_alien_attacks++;
        break;
    case "H":
        $nr_trader_found++;
        break;
    case "V":
        $nr_fleet_lost++;
        break;
    }
    
}
$metall = to_formatted($metall);
$crystal = to_formatted($crystal);
$deuterium = to_formatted($deuterium);
$darkmatter = to_formatted($darkmatter);

?>

<div style="overflow: hidden;">
<div style="margin-bottom: 20px; float: left;">
<table class="tight">
  <thead>
    <tr>
      <th>Geflogene Expeditionen:</th>
      <th><?php echo count($expos);?></th>
    <tr>
  </thead>
  <tbody>
    <tr>
      <td>Ressourcenfunde:</td>
      <td><?php echo $nr_res_found;?></td>
    </tr>
    <tr>
      <td>DM-Funde:</td>
      <td><?php echo $nr_dm_found;?></td>
    </tr>
    <tr>
      <td>Flottenfunde:</td>
      <td><?php echo $nr_fleet_found;?></td>
    </tr>
    <tr>
      <td>Piratenangriffe:</td>
      <td><?php echo $nr_pirate_attacks;?></td>
    </tr>
    <tr>
      <td>Alienangriffe:</td>
      <td><?php echo $nr_alien_attacks;?></td>
    </tr>
    <tr>
      <td>Flottenverluste:</td>
      <td><?php echo $nr_fleet_lost;?></td>
    </tr>
    <tr>
      <td>Händlerfunde:</td>
      <td><?php echo $nr_trader_found;?></td>
    </tr>
  </tbody>
</table>
</div>
<div style="margin-bottom: 20px; float: left;">
<table class="tight">
  <thead>
    <tr>
      <th colspan="2">Ressourcenfunde gesamt</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Metall:</td>
      <td><span class="resource"><?php echo $metall;?></span></td>
    </tr>
    <tr>
      <td>Kristall:</td>
      <td><span class="resource"><?php echo $crystal;?></span></td>
    </tr>
    <tr>
      <td>Deuterium:</td>
      <td><span class="resource"><?php echo $deuterium;?></span></td>
    </tr>
    <tr>
      <td>Dunkle Materie:</td>
      <td><span class="resource"><?php echo $darkmatter;?></span></td>
    </tr>
</table>
</div>
<div style="margin-bottom: 20px; float: left;">
<table class="tight">
  <thead>
    <tr>
      <th colspan="2">Flottenfunde gesamt</th>
    </tr>
  </thead>
  <tbody>
<?php
           foreach($fleet as $ship_key=>$ship_amount)
           {
               if($ship_amount == 0)
                   continue;
               $ship_name = $ship_names[$ship_key]['name'];

               echo "<tr>\n";
               echo "<td>$ship_name</td>\n";
               echo "<td>$ship_amount</td>\n";
               echo "</tr>\n";
           }
?>
  </tbody>
</table>
</div>
</div>
<h2>Detailansicht</h2>
<table>
  <thead>
    <tr>
      <th>Zeit</th>
      <th>Nutzer</th>
      <th>Art</th>
      <th>Fund</th>
      <th>Wert</th>
    </tr>
  </thead>
  <tbody>
<?php
foreach($expos as $expo)
{
    $time = explode(' ',$expo['Time'])[1];
    $type = get_expo_type($expo['Type']);

    $type_class = "";
    switch($expo['Type'])
    {
    case "R":
    case "F":
    case "D";
    case "H":
        $type_class = " good";
        break;
    case "P":
    case "A":
    case "V":
        $type_class = " bad";
    }

    // Get details of resource and fleet found
    $details = "";
    $value = "";
    
    if($expo['Type'] == "R" || $expo['Type'] == "D")
    {
        $amount = $expo['ResAmount'];
        $details = get_resource_name($expo['ResType']).": <span class=\"resource\">".to_formatted($amount)."</span>\n";

        if($expo['Type'] == "R")
        {
            $multiplier = 0;
            switch($expo['ResType'])
            {
            case "metall":
                $multiplier = 1;
                break;
            case "crystal":
                $multiplier = 2;
                break;
            case "deuterium":
                $multiplier = 3;
                break;                       
            }
            $value = $multiplier*$amount;
        }
    }
    elseif($expo['Type'] == "F")
    {
        $fleet = $expo['Fleet'];
        $fleet = explode(",", $fleet);

        $details = array();
        $value = 0;
        foreach($fleet as $ship_details)
        {
            $ship_details = explode(": ", $ship_details);
            $ship_type = $ship_details[0];
            $ship_amount = $ship_details[1];

            array_push($details, $ship_names[$ship_type]['name'].": ".$ship_amount);
            
            $value = $value + $ship_amount*get_ship_value($ship_type);
        }
        $details = implode("<br>", $details);        
    }    
    
    echo "<tr>\n";
    echo "  <td><span class=\"cell_time\">$time</span></td>\n";
    echo "  <td><span class=\"user_name\">".$expo['UserName']."</span></td>\n";
    echo "  <td><span class=\"cell_type$type_class\">$type</span></td>\n";
    echo "  <td><div class=\"cell_details\">$details</div></td>\n";
    echo "  <td><span class=\"cell_value\">".to_formatted($value)."</span></td>\n";
    echo "</tr>\n";
}
?>
  </tbody>
</table>
