<?php

$page_loaded = false;
$active_page = "";

function load_page($file, $navitem)
{
    global $page_loaded;
    global $active_page;

    $active_page = $navitem;
    
    // Page already generated
    if ($page_loaded) return;

    $page_loaded = true;

    // Load header 
    include("header.php");

    $pass = "";
    if(isset($_POST['password']))
    {
        $pass = $_POST['password'];
        setcookie("password", $pass, time()+2592000, "/"); // store as cookie for 30 days
    }
    if(empty($pass) and isset($_COOKIE['password']))
        $pass = $_COOKIE['password'];
    
    $pass_correct = strcmp($pass,"makeNAgreatagain") == 0;
    if($pass_correct)
    {    
        // Content of the page
        include($file);
    }
    else
    {
        $pass_form = <<<P
<form action="" method="POST">
<label for="password">Passwort:</label> <input type="password" id="password" name="password"><br /><br />
        <button type="submit">Einloggen</button>
</form>
P;
        echo $pass_form;
    }

    // Load footer
    include("footer.php");
    
    exit;
}

?>
