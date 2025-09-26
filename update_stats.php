<?php
require_once("include/expo_functions.php");

echo "====================\n";
echo "Time: ".date('d-m-y h:i:s')."\n";
echo "Updating highscore table\n";

update_highscore();
?>
