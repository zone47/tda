<?php
/* / */

set_time_limit(360000);
include "functions.php";
include "config_harvest.php";


list($g_usec, $g_sec) = explode(" ",microtime());
define ("t_start", (float)$g_usec + (float)$g_sec);

include "harvest.php";

include "occurences.php";

list($g2_usec, $g2_sec) = explode(" ",microtime());
define ("t_end", (float)$g2_usec + (float)$g2_sec);
print round (t_end-t_start, 1)." secondes"; 



?>