<?php
    $file = '23333.php';
    while (1) {
        unlink($file);
        usleep(500);
    }
?>
