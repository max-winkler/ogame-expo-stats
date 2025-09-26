<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$ship_names = array("transporterSmall"=>array
                    ('name'=>"Kleiner Transporter",
                     'metall'=>"2000",
                     'crystal'=>"2000",
                     'deuterium'=>"0"),
                    "transporterLarge"=>array
                    ('name'=>"Großer Transporter",
                     'metall'=>"6000",
                     'crystal'=>"6000",
                     'deuterium'=>""),
                    "colonyShip"=>array
                    ('name'=>"Kolonieschiff",
                     'metall'=>"10000",
                     'crystal'=>"20000",
                     'deuterium'=>"10000"),
                    "recycler"=>array
                    ('name'=>"Recycler",
                     'metall'=>"10000",
                     'crystal'=>"6000",
                     'deuterium'=>"2000"),
                    "espionageProbe"=>array
                    ('name'=>"Spionagesonde",
                     'metall'=>"0",
                     'crystal'=>"1000",
                     'deuterium'=>"0"),
                    "fighterLight"=>array
                    ('name'=>"Leichter Jäger",
                     'metall'=>"3000",
                     'crystal'=>"1000",
                     'deuterium'=>"0"),
                    "figherHeavy"=>array
                    ('name'=>"Schwerer Jäger",
                     'metall'=>"6000",
                     'crystal'=>"4000",
                     'deuterium'=>"0"),
                    "cruiser"=>array
                    ('name'=>"Kreuzer",
                     'metall'=>"20000",
                     'crystal'=>"7000",
                     'deuterium'=>"2000"),
                    "battleship"=>array
                    ('name'=>"Schlachtschiff",
                     'metall'=>"45000",
                     'crystal'=>"15000",
                     'deuterium'=>""),
                    "interceptor"=>array
                    ('name'=>"Schlachtkreuzer",
                     'metall'=>"30000",
                     'crystal'=>"40000",
                     'deuterium'=>"15000"),
                    "bomber"=>array
                    ('name'=>"Bomber",
                     'metall'=>"50000",
                     'crystal'=>"25000",
                     'deuterium'=>"15000"),
                    "destroyer"=>array
                    ('name'=>"Zerstörer",
                     'metall'=>"60000",
                     'crystal'=>"50000",
                     'deuterium'=>"15000"),
                    "deathstar"=>array
                    ('name'=>"Todesstern",
                     'metall'=>"5000000",
                     'crystal'=>"4000000",
                     'deuterium'=>"1000000"),
                    "reaper"=>array
                    ('name'=>"Reaper",
                     'metall'=>"85000",
                     'crystal'=>"55000",
                     'deuterium'=>"20000"),
                    "explorer"=>array
                    ('name'=>"Pathfinder",
                     'metall'=>"8000",
                     'crystal'=>"15000",
                     'deuterium'=>"8000"));

$ship_abbrevs = array("transporterSmall"=>"KT",
                    "transporterLarge"=>"GT",
                    "colonyShip"=>"KS",
                    "recycler"=>"R",
                    "espionageProbe"=>"SP",         
                    "fighterLight"=>"LJ",
                    "figherHeavy"=>"SJ",
                    "cruiser"=>"K",
                    "battleship"=>"SS",
                    "interceptor"=>"SK",
                    "bomber"=>"B",
                    "destroyer"=>"Z",
                    "deathstar"=>"TS",
                    "reaper"=>"RE",
                    "explorer"=>"PF");
$resource_names = array("metall"=>"Metall",
                        "crystal"=>"Kristall",
                        "deuterium"=>"Deuterium",
                        "darkmatter"=>"Dunkle Materie");
$expo_types = array("R"=>"Ressourcenfund",
                    "H"=>"Händler",
                    "N"=>"Neutrales Ereignis",
                    "F"=>"Flottenfund",
                    "P"=>"Piratenangriff",
                    "A"=>"Alienangriff",
                    "V"=>"Verlust",
                    "D"=>"Dunkle Materie",
                    "I"=>"Itemfund",
                    "L"=>"Lebensformevent");
    
$pirate_attack_keywords = array("verzweifelte Weltraumpiraten",
                                "primitive Barbaren",
                                "betrunkener Piraten",
                                "gegen einige Piraten wehren",
                                "Zusammentreffen mit einigen Weltraumpiraten",
                                "von einer geheimen Piratenbasis",
                                "Hinterhalt einiger Sternen-Freibeuter",
                                "als böse Falle einiger arglistiger");
$alien_attack_keywords = array("Wir hatten Mühe den korrekten Dialekt einer Alienrasse auszusprechen",
                               "Erstkontakt mit einer unbekannten Spezies",
                               "fremdartig anmutende Schiffe",
                               "unbekannte Spezies greift unsere Expedition an",
                               "von einer kleinen Gruppe unbekannter Schiffe angegriffen",
                               "in eine Alien-Invasions-Flotte geraten",
                               "steht die Flotte unter schwerem Feuer",
                               "Deine Expeditionsflotte hat anscheinend das Hoheitsgebiet einer bisher unbekannten, aber äußerst aggressiven und kriegerischen Alienrasse verletzt.");
$fleet_lost_keywords = array("die Flotte endgültig verloren",
                             "nur noch folgender Funkspruch übrig geblieben",
			     "Nahaufnahmen eines sich öffnenden schwarzen Lochs",
		     	     "Ein Kernbruch des Führungsschiffes");
$trader_found_keywords = array("Repräsentanten mit Tauschwaren",
                               "Exklusivkunden in sein schwarzes Buch aufzunehmen");
$item_found_keywords = array("einen Gegenstand sicherstellen",
                             "Unsere Flotte hat ein wertvolles Artefakt gefunden.");

$date_time_format = "dd.mm.yyyy H:i:s";

function get_expo_type($key)
{
    global $expo_types;
    return $expo_types[$key];
}

function get_resource_name($key)
{
    global $resource_names;
    return $resource_names[$key];
}

function parse_fleet($report)
{
    global $ship_names;

    preg_match("/Beute\s+((?:.*\n?)*)/", $report, $fleet_details);
    
    $fleet_details = trim($fleet_details[1]);
    $fleet_details = preg_split('/:|\n/', $fleet_details);
    $fleet_details = array_map('trim', $fleet_details);
    $fleet = array();
            
    for($i=0; $i<count($fleet_details)/2; $i++)
    {
        $type = $fleet_details[2*$i];
        $amount = $fleet_details[2*$i+1];

        $ship_key = array_keys(array_filter($ship_names, function($x) USE($type) {
                    return strcmp($x['name'], $type) == 0;
                }));        
        
        if(count($ship_key) === 0)
            echo "FEHLER: Schiffe mit dem Namen $type sind unbekannt.<br>";

        $fleet[$ship_key[0]] = preg_replace('/(?<=\d)\.(?=\d)/','',$amount);
    }
    
    return $fleet;    
}
function parse_reports($reports_string)
{
    global $resource_names;
    global $ship_names;
    
    // $reports = explode("Expeditionsergebnis", $reports_string);
    $reports = preg_split('/(Expeditionsergebnis|Lebensformbericht)/', $reports_string);
    $reports = array_map('trim', $reports);
    $reports = array_filter($reports);

    $results = array();
    
    foreach($reports as $report)
    {
        $report = str_replace("Mehr Details", "", $report);
        // Read time of expo report
        preg_match('/\d{2}(.)\d{2}(.)\d{4}( )\d{2}(:)\d{2}(:)\d{2}/', $report, $time);
        $time = $time[0];

        $result = array();
        $result['time'] = $time;

        $ship_names_pattern = implode('|', array_map(function($ship) {
            return preg_quote($ship['name'], '/');
        }, $ship_names));        
        
        // Fleet found
        if(preg_match_all('/Beute\s+(' . $ship_names_pattern . ')\s+([\d\.]+)/',
                          $report, $matches)) {            
            $result['type'] = "F";
            $result['fleet'] = parse_fleet($report);
        }
        // Life form event
        elseif(strpos($report, 'Heimatplanet: ') !== false)
        {
            $result['type'] = "L";
        }
        // Resources found        
        elseif(preg_match('/Beute\s+(Metall|Kristall|Deuterium|Dunkle Materie)\s+([\d\.]+)/', $report, $resource_string))
        {   
            $type = $resource_string[1];
            $amount = $resource_string[2];
            
            $amount = str_replace(".", "", $amount);
            
            // Check if dark matter was found
            if($type=="Dunkle Materie")
            {
                $result['type'] = "D";
            }
            // Otherwise resources were found
            else
            {
                $result['type'] = "R";
            }

            $resource_key = array_search($type, $resource_names);

            $result['resource'] = array();
            $result['resource'][$resource_key] = $amount;            
        }
        elseif(is_pirate_attack($report))
        {
            $result['type'] = "P";
        }
        elseif(is_alien_attack($report))
        {
            $result['type'] = "A";
        }
        elseif(is_fleet_lost($report))
        {
            $result['type'] = "V";
        }
        elseif(is_trader_found($report))
        {
            $result['type'] = "H";
        }
        elseif(is_item_found($report))
        {
            $result['type'] = "I";
        }
        else
        {
            $result['type'] = "N";
        }
        array_push($results, $result);
    }
    return $results;
}

function is_pirate_attack($report)
{
    global $pirate_attack_keywords;
    
    foreach ($pirate_attack_keywords as $keyword)
        if (strpos($report, $keyword) !== false)
            return true;        
    
    return false;
}

function is_alien_attack($report)
{
    global $alien_attack_keywords;
    
    foreach ($alien_attack_keywords as $keyword)
        if (strpos($report, $keyword) !== false)
            return true;        
    
    return false;
}

function is_fleet_lost($report)
{
    global $fleet_lost_keywords;
    
    foreach ($fleet_lost_keywords as $keyword)
        if (strpos($report, $keyword) !== false)
            return true;        
    
    return false;
}

function is_trader_found($report)
{
    global $trader_found_keywords;
    
    foreach ($trader_found_keywords as $keyword)
        if (strpos($report, $keyword) !== false)
            return true;        
    
    return false;
}

function is_item_found($report)
{
    global $item_found_keywords;
    
    foreach ($item_found_keywords as $keyword)
        if (strpos($report, $keyword) !== false)
            return true;        
    
    return false;
}


function to_formatted($x)
{
    if(empty($x)) return "";
    return number_format(floatval($x), 0, "," , "." ); 
}

function get_ship_value($ship)
{
    global $ship_names;
    return $ship_names[$ship]['metall'] + 2*$ship_names[$ship]['crystal'] + 3*$ship_names[$ship]['deuterium'];
}

function connect_db()
{
    try
    {
        $db = new PDO("mysql:host=localhost;dbname=ExpoStats;charset=utf8", "php_user", "2sPY1EmesFoFwMQe");
    }
    catch (PDOException $e)
    {
        echo "Access to database denied<br>\n";
        return false;
    }
    
    $db->query('set profiling=1');
    return $db;
}

function get_all_users($db)
{
    $query = "SELECT * FROM User";
    $results = $db->query($query);
    return $results->fetchAll(PDO::FETCH_ASSOC);
}

function add_user($db, $name)
{
    $command = $db->prepare("INSERT INTO User (Id, Name) VALUES (NULL, ?)");
    $command->execute(array($name));

    $query = "SELECT Id FROM User WHERE Name = '$name'";
    $query = $db->query($query);
    $result = $query->fetch();

    return $result['Id'];
}

function get_user_name($db, $id)
{
    $query = "SELECT Name FROM User WHERE Id = '$id'";
    $results = $db->query($query);
    return $results->fetch()['Name'];
}

function get_user_by_name($db, $name)
{
    $stmt = $db->prepare("SELECT Id FROM User WHERE Name = ?");
    $stmt->execute(array($name));
    return $stmt->fetch()['Id'];
}
function add_expo_report($db, $userId, $report)
{
    $res = array('status' => 0);
    
    // Skip lifeform reports
    if($report['type'] == 'L')
        return $res;
    
    $time = DateTime::createFromFormat("d.m.Y H:i:s", $report['time']);

    // Insert expedition instance
    $command = $db->prepare("INSERT INTO Expeditions (Id, User, Type, Time) VALUES(NULL, ?, ?, ?)");
    $result = $command->execute(array($userId, $report['type'], $time->format("Y-m-d H:i:s")));

    if($result === false)
    {
        $error = $command->errorInfo()[1];
        if($error == 1062)
            $res['status'] = 1; // duplicate expo
        else
            $res['status'] = 2; // unknown error

        return $res;
    }
        
    $Id = $db->lastInsertId();

    // Insert expedition details
    switch($report['type'])
    {
    case 'R':
    case 'D':
        $resources = $report['resource'];
        foreach($resources as $resource_key=>$amount)
        {
            // Compute value
            switch($resource_key)
            {
            case 'metall':
                $value = $amount;
                break;
            case 'crystal':
                $value = 2*$amount;
                break;
            case 'deuterium':
                $value = 3*$amount;
                break;
            }
            if($report['type'] == 'R')
                $res['value'] = $value;
            
            $command = $db->prepare("INSERT INTO ResFound (Expedition, Type, Amount) VALUES (?,?,?)");
            $result = $command->execute(array($Id, $resource_key, $amount));

            if($result === false)
            {
                $res['status'] = 2; // unknown error                
                return $res;
            }
        }
        break;
    case 'F':
        $fleet = $report['fleet'];

        // Compute fleet value
        $value = 0;
        
        foreach($fleet as $ship=>$amount)
            $value = $value + $amount*get_ship_value($ship);

        $res['value'] = $value;       

        // Insert fleet information into database
        foreach($fleet as $ship=>$amount)
        {
            $command = $db->prepare("INSERT INTO FleetFound (Expedition, Ship, Amount) VALUES(?,?,?)");
            $result = $command->execute(array($Id, $ship, $amount));

            if($result === false)
            {
                $res['status'] = 2; // unknown error
                return $res;
            }
        }
        
        break;            
    }

    post_discord($report, get_user_name($db, $userId));
    
    return $res;
}

function get_expos($db, $begin = NULL, $end = NULL, $user = NULL)
{
    $begin_str = $begin->format('Y-m-d H:i:s');
    $end_str = $end->format('Y-m-d H:i:s');
    
    $query = "SELECT Expeditions.Id, Expeditions.Time, Expeditions.User, Expeditions.Type,
	User.Name As UserName,
    ResFound.Type as ResType, ResFound.Amount AS ResAmount,
    GROUP_CONCAT(FleetFound.Ship, ': ',FleetFound.Amount) AS Fleet
FROM Expeditions
JOIN User ON Expeditions.User = User.Id
LEFT JOIN ResFound ON Expeditions.Id = ResFound.Expedition
LEFT JOIN FleetFound ON Expeditions.Id = FleetFound.Expedition
LEFT JOIN Ships ON FleetFound.Ship = Ships.Id ";

    $conditions = array();
    if(!is_null($user))
        array_push($conditions, "Expeditions.User = :userId ");
    if(!is_null($begin))
        array_push($conditions, "Expeditions.Time >= :begin");
    if(!is_null($end))
        array_push($conditions, "Expeditions.Time <= :end");

    if(count($conditions)>0)
        $query .= "WHERE ".implode(" AND ", $conditions);
    $query .= " GROUP BY Expeditions.Id
                    ORDER BY Expeditions.Time DESC";    

    $sth = $db->prepare($query);

    if(!is_null($user))
        $sth->bindParam(":userId", $user);
    if(!is_null($begin))
        $sth->bindParam(":begin", $begin_str);
    if(!is_null($begin))
        $sth->bindParam(":end", $end_str);
    $sth->execute();
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

function get_top_res_found($db, $res_type, $limit=3)
{
    $sth = $db->prepare("SELECT Expeditions.*, User.Name, ResFound.Amount
FROM Highscore
JOIN Expeditions ON Highscore.Expedition = Expeditions.Id
JOIN ResFound ON ResFound.Expedition = Highscore.Expedition
JOIN User ON User.Id = Expeditions.User
WHERE Highscore.Type = :type
ORDER BY Highscore.Rank ASC");
    
    $sth->bindParam(":type", $res_type);

    $sth->execute();
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

function get_top_fleet($db, $limit=3)
{
    $sth = $db->prepare("SELECT Expeditions.Id, Expeditions.Time, Expeditions.User, User.Name,
	GROUP_CONCAT(Ships.Name, ': ', FleetFound.Amount) AS Fleet,
    SUM(FleetFound.Amount*(Ships.CostMetall+2*Ships.CostCrystal+3*CostDeuterium)) AS Value
FROM Highscore
JOIN Expeditions ON Expeditions.Id = Highscore.Expedition
JOIN User ON Expeditions.User = User.Id
JOIN FleetFound ON FleetFound.Expedition = Expeditions.Id
JOIN Ships ON FleetFound.Ship = Ships.Id
WHERE Highscore.Type = 'fleet'
GROUP BY Expeditions.Id
ORDER BY Highscore.Rank ASC");
    
    $sth->execute();
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

function get_expo_statistics($db, $userId, $time_key=NULL)
{
    global $expo_types;
    global $resource_names;
    global $ship_names;

    $statistics = array();

    $from_time = new DateTime();
    $to_time = new DateTime();

    $from_time->setTime(0, 0, 0);
    $to_time ->setTime(0, 0, 0);

    if(!empty($time_key))
    {                    
        switch($time_key) {
        case "today":
            // Reset to-time
            $to_time = new DateTime();
            break;
        case "yesterday":
            $from_time->sub(new DateInterval("P1D"));
            break;
        case "this_week":
            if ($from_time->format('N') != 1)
                $from_time->modify('last monday');
            $to_time = new DateTime();
            break;
        case "last_week":
            if ($from_time->format('N') != 1)
                $from_time->modify('last monday');
            $to_time = clone $from_time;
            $from_time->sub(new DateInterval("P7D"));
            break;
        case "this_month":
            $from_time = DateTime::createFromFormat('U', strtotime('first day of this month'));
            $to_time = new DateTime();            
            break;
        case "last_month":
            $from_time = DateTime::createFromFormat('U', strtotime('first day of last month'));
            $to_time = DateTime::createFromFormat('U', strtotime('first day of this month'));
            break;
        default:
            $time_key = "";
        }

        if(!empty($time_key))
        {
            $from_time_string = $from_time->format("Y-m-d H:i:s");
            $to_time_string = $to_time->format("Y-m-d H:i:s");
            
            $time_filter = "AND Expeditions.Time >= :from_time AND Expeditions.Time <= :to_time";
        }
    }
    else
        $time_filter = "";

    if(!empty($userId))
        $user_filter = " AND Expeditions.User = :userId";
    else
        $user_filter = "";

    // Get number of expeditions
    $cmd = "SELECT COUNT(*) AS NrExpeditions FROM Expeditions WHERE true $time_filter $user_filter";

    $sth = $db->prepare($cmd);
    if(!empty($userId))
        $sth->bindParam(":userId", $userId);
    if(!empty($time_key))
    {
        $sth->bindParam(":from_time", $from_time_string);
        $sth->bindParam(":to_time", $to_time_string);
    }
    
    $sth->execute();

    $result = $sth->fetch(PDO::FETCH_ASSOC);
    $statistics['NrExpeditions'] = $result['NrExpeditions'];
    
    foreach($expo_types as $expo_key=>$expo_type_name)
    {
        $cmd = "SELECT COUNT(*) AS NrExpeditions FROM Expeditions WHERE Expeditions.Type = :expo_key $time_filter";
            
        if(!empty($userId))
            $cmd .= " AND User = :userId";

        $sth = $db->prepare($cmd);
        $sth->bindParam(":expo_key", $expo_key);
        if(!empty($userId))
            $sth->bindParam(":userId", $userId);
        if(!empty($time_key))
        {
            $sth->bindParam(":from_time", $from_time_string);
            $sth->bindParam(":to_time", $to_time_string);
        }
        
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $statistics[$expo_key] = $result['NrExpeditions'];        
    }

    foreach($resource_names as $res_type=>$res_name)
    {
        $cmd = "SELECT SUM(ResFound.Amount) AS :type 
                FROM Expeditions 
                JOIN ResFound ON ResFound.Expedition = Expeditions.Id 
                WHERE ResFound.Type = :type $time_filter";
        if(!empty($userId))
            $cmd .= " AND Expeditions.User = :userId";

        $sth = $db->prepare($cmd);
        $sth->bindParam(":type", $res_type);
        if(!empty($userId))
            $sth->bindParam(":userId", $userId);
        if(!empty($time_key))
        {
            $sth->bindParam(":from_time", $from_time_string);
            $sth->bindParam(":to_time", $to_time_string);
        }
        
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $statistics[$res_type] = $result[$res_type];
    }

    $cmd = "SELECT
    Ships.Id,
    Ships.Name,
    SUM(FleetFound.Amount) AS Amount
FROM
    Ships
JOIN
    FleetFound
ON
    FleetFound.Ship = Ships.Id
JOIN 
    Expeditions
ON
    Expeditions.Id = FleetFound.Expedition $user_filter
WHERE 
    true $time_filter
GROUP BY
    Ships.Id
";

    $sth2 = $db->prepare($cmd);
    if(!empty($userId))
        $sth2->bindParam(":userId", $userId);
    if(!empty($time_key))
    {
        $sth2->bindParam(":from_time", $from_time_string);
        $sth2->bindParam(":to_time", $to_time_string);
    }
    $sth2->execute();

    while($ship_stats = $sth2->fetch())
    {
        $statistics[$ship_stats['Id']] = $ship_stats['Amount'];
    }
    /*
    foreach($ship_names as $ship_type=>$ship_details)
    {
        $cmd = "SELECT SUM(Amount) AS :type
                FROM Expeditions
                JOIN FleetFound ON Expeditions.Id = FleetFound.Expedition
                WHERE FleetFound.Ship = :type $time_filter";
        if(!empty($userId))
            $cmd .= " AND Expeditions.User = :userId";
        
        $sth = $db->prepare($cmd);
        $sth->bindParam(":type", $ship_type);
        if(!empty($userId))
            $sth->bindParam(":userId", $userId);
        if(!empty($from_time))
            $sth->bindParam(":from_time", $from_time_string);
        
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $statistics[$ship_type] = $result[$ship_type];
    }
    */

    /*
    $stmt = $db->query('show profiles');    
    while($record = $stmt->fetch())
    {
        echo $record["Query"]." - ".$record["Duration"]."<br><br>";
    }
    */
    
    return $statistics;
}

function get_user_statistics($db, $user)
{
    $sth = $db->prepare("SELECT Date(Expeditions.Time) AS Date, User.Id, User.Name,
	(SELECT COUNT(*) 
    FROM Expeditions AS E 
    WHERE Date(E.Time) = Date 
    AND E.Time > curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY
    AND E.User = :user) AS NrExpos,
	SUM(
        (CASE 
         WHEN ResFound.Type = 'metall' THEN 1
         WHEN ResFound.Type = 'crystal' THEN 2
         WHEN ResFound.Type = 'deuterium' THEN 3
         END
        ) * ResFound.Amount
        ) AS ResValue,
    SUM(FleetFound.Amount * (Ships.CostMetall + 2*Ships.CostCrystal + 3*Ships.CostDeuterium)) AS FleetValue
   FROM Expeditions
   JOIN User ON User.Id = Expeditions.User
   LEFT JOIN ResFound ON ResFound.Expedition = Expeditions.Id
   LEFT JOIN FleetFound ON FleetFound.Expedition = Expeditions.Id
   LEFT JOIN Ships ON FleetFound.Ship = Ships.Id 
   WHERE User.Id = :user
   AND Time > curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY
   GROUP BY Date, User.Id
   ORDER BY Date ASC");

    $sth->bindValue(":user", $user, PDO::PARAM_INT);
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    return $results;        
}

function get_mse_stats($db)
{
    $sth = $db->prepare("SELECT User.Id, User.Name,
	COUNT(*) AS NrExpos,
	SUM(
        (CASE 
         WHEN ResFound.Type = 'metall' THEN 1
         WHEN ResFound.Type = 'crystal' THEN 2
         WHEN ResFound.Type = 'deuterium' THEN 3
         END
        ) * ResFound.Amount
        ) AS ResValue
   FROM User
   JOIN Expeditions ON Expeditions.User = User.Id
     AND Time > curdate() - INTERVAL 30 DAY   
   JOIN ResFound ON ResFound.Expedition = Expeditions.Id   
   GROUP BY User.Id
   ORDER BY ResValue DESC");

    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    return $results;        
}

function post_discord($report, $user)
{
    global $resource_names;
    global $ship_names;

    $res_found_message = array("Wahnsinn! **:user** hat richtig viele Ressourcen auf Expo gefunden. Er/sie kann sich nun über __:res_amount :res_type__ freuen. Freuen wir uns mit ihm/ihr.",
                               "Auch ein blindes Huhn findet mal ein Korn. Oder in diesem Fall __:res_amount :res_type__. Glückwunsch an **:user**!",
                               "Heute ist **:user**'s Glückstag. Er/sie hat __:res_amount :res_type__ auf einer Expedition gefunden.",
                               "Gute Neuigkeiten! **:user**'s Expeditionsflotte kommt mit __:res_amount :res_type__ zurück. Eine richtig tolle Leistung.");
    $fleet_found_message = array("**:user** hat eine richtig dicke Flotte auf einer Expedition gefunden! Gratulieren wir ihm/ihr zu".PHP_EOL.":fleet_details",
                                 "**:user** hat sage und schreibe".PHP_EOL.":fleet_details".PHP_EOL."auf einer Expedition gefunden. Damit sollten wir für den Krieg gegen TNT gerüstet sein.",
                                 "Ein beachtlicher Flottenfund von **:user** auf einer Expedition:".PHP_EOL.":fleet_details".PHP_EOL."Damit könnte er/sie Kaktus locker abschießen.",
				 "Toll! **:user** hat".PHP_EOL.":fleet_details".PHP_EOL."auf einer Expedition gefunden. Pass gut darauf auf und das Saven nicht vergessen!",
			 	 "Freezer sollte sich ganz warm anziehen. **:user** hat soeben".PHP_EOL.":fleet_details".PHP_EOL."auf einer Expedition gefunden. Damit werden wir ihn vernichten!");
    $trader_found_message = array("Gute Nachrichten! **:user** hat gerade einen Händler auf einer Expedition gefunden.",
                                  "**:user**'s Bemühungen haben sich ausgezahlt. Er/sie hat einen Händler auf einer Expedition gefunden.",
                                  "Das war eine richtig Erfolgreiche Expedition von **:user**. Er/sie hat nun einen neuen Händler.");
    $fleet_lost_message = array("Etwas sehr ärgerliches ist passiert. **:user** hat seine/ihre Expeditionsflotte verloren. Spenden wir ihm/ihr etwas Trost.",
                                "Das lief ja mal richtig beschissen. **:user**'s Flotte hat die letzte Expedition nicht überlebt.",
				"Armer **:user**. Seine/ihre Expeditionsflotte wurde zerstört. Jetzt bloß nicht den Kopf in den Sand stecken!",
				"Scheiße, Scheiße, Scheiße, Scheiße! **:user** hat seine Expeditionsflotte verloren.");
    $low_res_found_message = array("Was für ein lächerlicher Ressourcenfund von **:user**. Seine Expedition hat lediglich __:res_amount :res_type__ eingebracht");

    $msg = "";
    $res_type = "";
    $res_amount = "";
    $fleet_details = "";
    
    if($report['type'] == 'H')
    {
        $i = rand(0,count($trader_found_message)-1);
        $msg = $trader_found_message[$i];        
    }
    elseif($report['type'] == 'R')
    {
        $resource_details = $report['resource'];
        
        foreach($resource_details as $resource_key=>$amount)
        {
            $value = 0;
            switch($resource_key)
            {
            case 'metall':
                $value = $amount;
                break;
            case 'crystal':
                $value = 2*$amount;
                break;
            case 'deuterium':
                $value = 3*$amount;
                break;
            case 'darkmatter':
                $value = 0;
                break;            
            }

            $res_type = $resource_names[$resource_key];
            $res_amount = to_formatted($amount);
            
            if($value > 120000000)
            {
                $i = rand(0,count($res_found_message)-1);
                $msg = $res_found_message[$i];     
            }

            /*
            if($value > 0 and $value < 800000)
            {
                $i = rand(0,count($low_res_found_message)-1);
                $msg = $low_res_found_message[$i];     
            }
            */
            
        }       
    }
    elseif($report['type'] == 'F')
    {
        $fleet = array();
        $fleet_details = $report['fleet'];

        $value = 0;
        foreach($fleet_details as $ship_key=>$amount)
            $value = $value + $amount*get_ship_value($ship_key);

        if($value > 120000000)
        {
            foreach($fleet_details as $ship_key=>$amount)
                array_push($fleet, $amount." ".$ship_names[$ship_key]['name']);

            $i = rand(0,count($fleet_found_message)-1);
            $msg = $fleet_found_message[$i];

            $msg = $msg.PHP_EOL.PHP_EOL."__Gesamtwert__: ".to_formatted($value );
            
            $fleet_details = implode(PHP_EOL, $fleet);
        }
    }
    elseif($report['type'] == 'V')
    {
        $i = rand(0,count($fleet_lost_message)-1);
        $msg = $fleet_lost_message[$i];        
    }
    //elseif($report['type'] == 'P')
    //{
    //    $msg = "?gay";
    //}

    if(empty($msg))
        return;

    $msg = str_replace(":user", $user, $msg);
    $msg = str_replace(":res_type", $res_type, $msg);
    $msg = str_replace(":res_amount", $res_amount, $msg);
    $msg = str_replace(":fleet_details", $fleet_details, $msg);
    
    // echo "Der Discord-Bot wird den Anderen von diesem unglaublichen Erfolg berichten.<br>";
    
    $webhook = "https://discord.com/api/webhooks/815333180113420298/G9RDIS_Mk2kZbB8iXtF4hG3uWAy7cnbVgH1Y_q5BtPkTN0fu-xkineyd3-N7JpxXmGg8";

    $timestamp = date("c", strtotime("now"));

    $json_data = json_encode([
        "content" => $msg,
        "username" => "ExpoStats-Bot",
        "tts" => false,
        "embeds" => [
            [
                "title" => "Neuigkeiten von unseren Expeditionen",
                "type" => "rich",
                "description" => "",
                "url" => "https://moakatanga.ddns.net/overview.php",
                "timestamp" => $timestamp,
                "color" => hexdec( "3366ff" ),
            ]
        ]
    ]);


    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    curl_close($ch);
}

function get_fleet_speed($combustion_lvl, $impulse_lvl, $hyperspace_lvl)
{
    // Basic speed
    $ship= array(
        "fighterLight"=>array("speed"=>12500, "type"=>"combustion"),
        "transporterSmall"=>array("speed"=>5000, "type"=>"combustion"),
        "recycler"=>array("speed"=>2000, "type"=>"combustion"),
        "transporterLarge"=>array("speed"=>7500, "type"=>"combustion"),
        "fighterHeavy"=>array("speed"=>10000, "type"=>"impulse"),
        "colonyShip"=>array("speed"=>2500, "type="=>"impulse"),
        "cruiser"=>array("speed"=>15000, "type"=>"impulse"),
        "bomber"=>array("speed"=>4000, "type"=>"impulse"),
        "battleship"=>array("speed"=>10000, "type"=>"hyperspace"),
        "interceptor"=>array("speed"=>10000, "type"=>"hyperspace"),
        "destroyer"=>array("speed"=>5000, "type="=>"hyperspace"),
        "deathstar"=>array("speed"=>100, "type"=>"hyperspace")
    );

    // For higher engine levels
    if($impulse_lvl >= 5)
        $ship['transporterSmall'] = array("speed"=>10000, "type"=>"impulse");

    if($impulse_lvl >= 17)
        $ship['recycler'] = array("speed"=>4000, "type"=>"impulse");

    if($hyperspace_lvl >= 8)
        $ship['bomber'] = array("speed"=>5000, "type"=>"hyperspace");

    if($hyperspace_lvl >= 15)
        $ship['recycler'] = array("speed"=>6000, "type"=>"hyperspace");

            
    $ship_speed = array();
    foreach($ship as $ship_key=>$details)
    {
        switch($details['type'])
        {
        case "combustion":
            $speed = $details['speed']*(1+0.1*$combustion_lvl);
            break;
        case "impulse":
            $speed = $details['speed']*(1+0.2*$impulse_lvl);
            break;
        case "hyperspace":
            $speed = $details['speed']*(1+0.3*$hyperspace_lvl);
            break;
        default:
            break;            
        }

        $ship_speed[$ship_key] = $speed;
    }

    return $ship_speed;
}

function update_highscore()
{
    global $resource_names;

    $db = connect_db();

    $from_time = DateTime::createFromFormat('U', strtotime('first day of this month'));
    $from_time = $from_time->format("Y-m-d");

    // Update resource highscore
    foreach($resource_names as $res_type=>$res_name)
    {
        $sth = $db->prepare("SELECT Expeditions.*, User.Name, ResFound.Amount
FROM Expeditions
JOIN ResFound ON ResFound.Expedition = Expeditions.Id AND ResFound.Type = :type
JOIN User ON User.Id = Expeditions.User
WHERE Expeditions.Time >= :time
ORDER BY ResFound.Amount DESC
LIMIT 5");
        
        $sth->bindParam(":type", $res_type);
        $sth->bindParam(":time", $from_time);
        
        $sth->execute();
        $i = 0;
        
        while($result = $sth->fetch()) {
            $i = $i+1;
            $sth2 = $db->prepare("REPLACE INTO Highscore (Type, Rank, Expedition) VALUE (?,?,?)");

            $sth2->execute([$res_type, $i, $result['Id']]);
        }
    }

    // Update fleet highscore
    $sth = $db->prepare("SELECT Expeditions.Id, Expeditions.Time, Expeditions.User, User.Name,
    SUM(FleetFound.Amount*(Ships.CostMetall+2*Ships.CostCrystal+3*CostDeuterium)) AS Value
FROM Expeditions
JOIN User ON Expeditions.User = User.Id
JOIN FleetFound ON FleetFound.Expedition = Expeditions.Id
JOIN Ships ON FleetFound.Ship = Ships.Id
WHERE Expeditions.Type = 'F' AND Expeditions.Time >= :time
GROUP BY Expeditions.Id
ORDER BY Value DESC
LIMIT 10");

    $sth->bindParam(":time", $from_time);
    
    $sth->execute();
    $i = 0;
             
    while($result = $sth->fetch())
    {
        $i = $i+1;
        $sth2 = $db->prepare("REPLACE INTO Highscore (Type, Rank, Expedition) VALUE ('fleet',?,?)");
        
        $sth2->execute([$i, $result['Id']]);
    }
}

?>
