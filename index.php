<?php
require_once("include/page.php");
require_once("include/expo_functions.php");

load_page(__FILE__, "manage");

$db = connect_db();

$add_expo_error = array(0 => "Erfolgreich hinzugefügt",
                        1 => "WARNUNG: Eine Expedition existiert schon zu genau dieser Zeit.",
                        2 => "FEHLER: Ein unbekannter Fehler ist beim Schreiben in die Datenbank aufgetreten.");

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="js/expeditions.js"></script>

    
<h1>Expeditionen hinzufügen</h1>
    
<?php

if(isset($_POST['add_user']))
{
    $name = $_POST['new_user'];
        
    if(!empty($name))
        $userId = add_user($db, $name);

    setcookie("user", $userId, time() + (86400 * 30), "/");
}
if(isset($_POST['save_reports']))
{
  session_start();
  if(isset($_SESSION['reports']))
  {           
      $reports = $_SESSION['reports'];
      $userId = $_SESSION['userId'];
      
      setcookie("user", $userId, time() + (86400 * 30), "/");
      
      foreach($reports as $report)
      {
          $res = add_expo_report($db, $userId, $report);

          // Print error or warning
          if($res['status'] != 0)
              echo '<span class="error">'.$add_expo_error[$res['status']].'</span>';
      }
  }
  session_destroy();
}

if(isset($_POST['read_reports']))
{
    // Parse report
    $reports_string = $_POST['reports'];
    $userId = $_POST['userId'];

    $reports = parse_reports($reports_string);

    session_start();
    $_SESSION['reports'] = $reports;
    $_SESSION['userId'] = $userId;

    global $ship_names;
    global $resource_names;
    
    // Write confirmation page
    echo "<h2>Eingelesene Expeditionsberichte</h2>\n";   

    echo "<p>Es wurden ".count($reports)." Expeditionsberichte eingelesen:</p>\n";
    echo "<ul>\n";
    foreach($reports as $report)
    {
        echo "<li>".$report['time'].": ";
        switch($report['type'])
        {
        case 'F':
            echo "<span class=\"good\">Flottenfund</span> (";
            $fleet = array();
            $fleet_details = $report['fleet'];

            foreach($fleet_details as $ship_key=>$amount)
                array_push($fleet, $ship_names[$ship_key]['name'].": ".$amount);
            echo implode(', ', $fleet).")";            
            break;
        case 'R':
            echo "<span class=\"good\">Resourcenfund</span> (";
            $resource_details = $report['resource'];

            foreach($resource_details as $resource_key=>$amount)
                echo $resource_names[$resource_key].": ".to_formatted($amount);
            echo ")";
            break;
        case 'D':
            echo "<span class=\"good\">Dunkle Materie</span> (".$report['resource']['darkmatter'].")";
            break;
        case 'I':
            echo "<span class=\"good\">Itemfund</span>";
            break;
        case 'H':
            echo "<span class=\"good\">Händlerfund</span>";
            break;                        
        case 'N':
            echo "Neutrales Ereignis";
            break;
        case 'A':
            echo "<span class=\"bad\">Alienangriff</span>";
            break;
        case 'P':
            echo "<span class=\"bad\">Piratenangriff</span>";
            break;
        case 'L':
            echo "Lebensformereignis";
            break;
        case 'V':
            echo "<span class=\"bad\">Flottenverlust</span>";
            break;
        }
        echo "</li>\n";
    }
    echo "</ul>\n";

    ?>
<div align="right">
  <form action="" method="post">
    <input type="button" onclick="javascript:history.go(-1)" value="Zurück"></input>
    <input name="save_reports" type="submit" value="Speichern"></input>
  </form>
</div>

<?php
}  
else
{
    if(isset($_COOKIE['user']) && empty($userId))
        $userId = $_COOKIE['user'];
  ?>
<h2>Expeditionsberichte einlesen</h2>
<form action="" method="post">
<?php
    $users = get_all_users($db);
?>
    <p>
    <b>Nutzer:</b>
    <select name="userId">
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
    <input class="new_user_form" name="new_user" style="display: none;">
    <input class="new_user_form" name="add_user" value="Hinzufügen" style="display: none;" type="submit">
    <a class="new_user_form" href="#" onclick="javascript:$('.new_user_form').toggle();">Neu</a>
  </p>
  <p>
    <b>Expeditionsberichte:</b><br>
    <textarea name="reports" style="width: 100%;" rows="10"></textarea><br>
    <div align="right">
      <input name="reset" value="Löschen" type="submit">
      <input name="read_reports" value="Auslesen" type="submit">
    </div>
  </p>
</form>
<h2>Tempermonkey Userskript</h2>
Zum Hinzufügen des User-Skript bitte <a href="expo_uploader.user.js">hier</a> klicken.
<br><br>
<a href="pretty_ogame.user.js">Hier</a> gibt es außerdem noch ein Skript, was ein Paar nützliche Funktionen zum Webinterface hinzufügt (Skip-Buttons in der Gala-Ansicht, Link im Werbebanner entfernt).
<h2>Hilfe</h2>
<ul>
  <li>Expeditionsberichte <u>inklusive Kopfzeile</u> markieren und
    kopieren</li>
  <li>In das Textfeld oben einfügen und mit <b>Auslesen</b> bestätigen</li>
  <li>Nutzernamen auswählen bzw. neu hinzufügen</li>
  <li>Richtigkeit der Angaben prüfen und mit <b>Speichern</b> bestätigen</li>
  <li>Bitte <u>keine gefaketen Berichte</u> und <u>keine Berichte aus anderen
  Universen</u> hinzufügen um die Statistik nicht zu verzerren</li>
  <li>Fehler an Moa Katanga melden</li>
</ul>
<div align="center">
  <img class="big_image" src="howto.jpg">
</div>

<?php
}
?>

