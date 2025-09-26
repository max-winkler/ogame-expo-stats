<?php

include("../include/expo_functions.php");

$user = $_POST['user'];
$time = $_POST['time'];
$report = $_POST['report'];
$type = $_POST['type'];

if(!isset($_POST['user']))
{
    echo json_encode(array('success' => 0, 'message' => "Kein User angegeben."));
    return;
}
if(!isset($_POST['time']))
{
    echo json_encode(array('success' => 0, 'message' => "Kein Zeitpunkt der Expedition angegeben."));
    return;
}
if(!isset($_POST['report']) || empty($_POST['report']))
{
    echo json_encode(array('success' => 0, 'message' => "Kein Expeditionsbericht angegeben."));
    return;
}

if(strcmp($type, "Expedition") != 0)
{
    $report = "Expeditionsergebnis ".$time." ".$report;
    $report = parse_reports($report)[0];

    $db = connect_db();
    $userId = get_user_by_name($db, $user);

    if(empty($userId))
    {
        echo json_encode(array('success' => 3, 'message' => "User $user nicht gefunden."));
        return;
    }
    
    $result = add_expo_report($db, $userId, $report);

    echo json_encode($result);
}

?>
