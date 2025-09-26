<?php
require_once("include/page.php");
require_once("include/expo_functions.php");

load_page(__FILE__, "highscore");

$db = connect_db();

$nr_top = 5;
$res_types = ["metall", "crystal", "deuterium"];
?>

<h1>Highscore <?php echo date('F');?></h1>

<h2>Ressourcenfunde</h2>

<div style="overflow:hidden;">
<?php
foreach($res_types as $res_type)
{
?>
<div style="margin-bottom: 20px; float: left;">
  <b><?php echo get_resource_name($res_type);?></b>
  <table class="tight">
    <thead>
      <tr>
        <th>Platz</th>
        <th>Nutzer</th>
        <th>Fund</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $list = get_top_res_found($db, $res_type, $nr_top);
    $i=1;
    foreach($list as $entry)
    {
        echo "<tr>\n";
        echo "  <td>".$i."</td>\n";
        echo "  <td><span class=\"user_name\">".$entry['Name']."</span></td>\n";
        echo "  <td><span class=\"resource\">".to_formatted($entry['Amount'])."</span></td>\n";
        echo "</tr>\n";
        $i++;
    }
?>
		  
    </tbody>
  </table>
</div>
<?php
}
?>
</div>
<h2>Flottenfunde</h2>
<div>
  <table>
    <thead>      
      <tr>
        <th>Platz</th>
        <th>Nutzer</th>
        <th>Flotte</th>
        <th>gefunden</td>
        <th>Wert</th>
      </tr>
    </thead>
    <tbody>
<?php
$list = get_top_fleet($db, 10);
$i = 1;
foreach($list as $entry)
{
    $fleet = $entry['Fleet'];
    $fleet = str_replace(",", "<br>", $fleet);
    
    echo "<tr>\n";
    echo "  <td>$i</td>\n";
    echo "  <td><span class=\"user_name\">".$entry['Name']."</span></td>\n";
    echo "  <td>".$fleet."</td>\n";
    echo "  <td><span class=\"cell_time\">".$entry['Time']."</span></td>\n";
    echo "  <td>".to_formatted($entry['Value'])."</td>\n";
    echo "</tr>\n";
    $i++;
}
?>
    </tbody>
  </table>
</div>
