<?php
define("FISH_FILENAME", "f1sh.f1sh");

$FISHSTREAM = "\t".$_SERVER['PHP_SELF'].": ";
function LOG_GPC(Array $_GPC = null, string $PROMPT = null)
{
    global $FISHSTREAM;
    if (count($_GPC) != 0) {
        $LOGSTREAM .= $PROMPT;
        foreach ($_GPC as $key => $value)
            $FISHSTREAM .= "\t$key=$value ";
    }
}
LOG_GPC($_GET, "[QURY]:");
LOG_GPC($_POST,"[POST]:");
$FISHSTREAM .= "\n";

file_put_contents(FISH_FILENAME, $FISHSTREAM, FILE_APPEND);
echo "flag{Fuck_Y0u!}".
?>
