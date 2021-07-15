password = 'xMu'

install = 'tar -cvf www.tar . && find . -name "*.php" -a -not -name "logger.php" | ' \
          'xargs sed -i "1i <?php include_once \'logger.php\'; ?>\n"'
uninstall = 'tar -cvf www.tar . && find . -name "*.php" -a -not -name "logger.php" | ' \
            'xargs sed -i "1d"'
monitor = 'find . -mtime -30m | xargs ls -aldF | awk \'{print $8 "  " $9}\' | sort -r'
log = 'tail -n 300 109'
log_filtered = 'tail -n 300 109.filtered'
log_attack = 'tail -n 300 109.attack.all'
