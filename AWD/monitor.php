<?php
/* @Wh1sper */
// date_default_timezone_set("Asia/Shanghai");

define("LOG_FILENAME", "109109109.109");
define("LOG_UPLOADFILE", "upload_stuff/");

$LOGSTREAM = "****************[".date('m-d H:i:s')."]****************[REMOTE_ADDR:$_SERVER[REMOTE_ADDR]]\n";
$LOGSTREAM .= $_SERVER['REQUEST_METHOD']." ".$_SERVER['REQUEST_URI']."\t===> "."$_SERVER[DOCUMENT_ROOT]".$_SERVER['SCRIPT_NAME']."\r\n";

if (array_key_exists('PATH_INFO', $_SERVER))
    $LOGSTREAM .= "PATH_INFO: ".$_SERVER['PATH_INFO']."\n";

function LOG_GPC(Array $_GPC = null, string $PROMPT = null)
{
    global $LOGSTREAM;
    if (count($_GPC) != 0) {
        $LOGSTREAM .= $PROMPT."\n";
        foreach ($_GPC as $key => $value)
            $LOGSTREAM .= "\t$key = $value\n";
    }
}
LOG_GPC($_GET, "[URL_PARAM]");
LOG_GPC($_POST, "[POST_DATA]");

if (count($_FILES) != 0) {
    if (!file_exists(LOG_UPLOADFILE))
        if (!mkdir(LOG_UPLOADFILE))
            $LOGSTREAM .= "mkdir(".LOG_UPLOADFILE.") FAILED. \n";
    $LOGSTREAM .= "!!FILE: \n";
    foreach ($_FILES as $key => $value) {
        $f1lename = LOG_UPLOADFILE.'['.$_SERVER['REMOTE_ADDR']."]".$value['name'].".orz";
        $LOGSTREAM .= "\t[$key] "."Name:$value[name]; Type:$value[type]; Size:$value[size]\n";
        if (copy($value['tmp_name'], $f1lename))
            $LOGSTREAM .= "\tSaved to \"$f1lename\"\n";
        else
            $LOGSTREAM .= "\tCan't copy file to \"$f1lename\"\n";
    }
}

LOG_GPC($_COOKIE, "COOKIE: "); // 记录Cookie，不需要的话删掉
$LOGSTREAM .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\n"; // 记录UA，不需要的话注释掉

$LOGSTREAM .= "\n";
file_put_contents(LOG_FILENAME, $LOGSTREAM, FILE_APPEND);
?>
