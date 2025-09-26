<!DOCTYPE html>
<html>
    <head>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="utf-8" http-equiv="encoding">
    <meta name="viewport" content="width=device-width
			     initial-scale=1.0 user-scalable=no">
    <link rel="stylesheet" href="css/style.css?v=1.1">
    <title>Miners &amp; More: Expo Stats</title>
  </head>
    <body>
      <?php
        $manage_active = "";
        $statistics_active = "";
        $overview_active = "";
        $highscore_active = "";

        global $active_page;

        if(strcmp($active_page, "manage") == 0)
            $manage_active = " class=\"active\"";
        elseif(strcmp($active_page, "overview") == 0)
            $overview_active = " class=\"active\"";
        elseif(strcmp($active_page, "statistics") == 0)
            $statistics_active = " class=\"active\"";
        elseif(strcmp($active_page, "highscore") == 0)
            $highscore_active = " class=\"active\"";
        ?>
    <div class="content">
      <div class="headline"><u>Miners &amp; More</u> <p style="margin-left: 50px;">Expo Statistics</p>
        <div class="navbar">
	<a href="index.php"<?php echo $manage_active;?>>Hinzufügen</a>
	<a href="overview.php"<?php echo $overview_active;?>>Übersicht</a>
	<a href="highscore.php"<?php echo $highscore_active;?>>Highscore</a>
	<a href="statistics.php"<?php echo $statistics_active;?>>Statistik</a>
        </div>
      </div>
      <div class="main">
        
