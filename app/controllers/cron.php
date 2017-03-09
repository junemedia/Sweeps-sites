<?php

class Cron extends CI_Controller
{
  protected $debug               = true;
  protected $error_log           = '';
  protected $error_log_email     = null;
  protected $error_log_file      = '/tmp/dailysweeps-cron-error.log';
  protected $WARN                = 3;
  protected $ERROR               = 2;
  protected $FATAL               = 1;
  protected $ERROR_STATUS_BY_INT = array(
                                     1 => 'FATAL',
                                     2 => 'ERROR',
                                     3 => 'WARN',
                                   );
  protected $yesterday;
  protected $today;

  public function __construct()
  {
    parent::__construct();

    if (@$_SERVER['HTTP_HOST']) {
      show_404();
    }

    $this->yesterday = date('Y-m-d', strtotime('-1 day'));
    $this->today     = date('Y-m-d');
  }

  public function __destruct()
  {
    if (!$this->error_log) {
      return;
    }
    @file_put_contents($this->error_log_file, $this->error_log, FILE_APPEND);
  }

  /**
   * Performs daily winner selection for the previous day on every rule_id that is active.
   * Sends emails to the winner and to Meredith about the winner selection.
   *
   * Should be invoked with the following crontab entry (should be midnight on the dot)
   * 0    0 * * * root /srv/sites/dailysweeps/bin/cron daily
   *
   * @return void
   */
  public function daily()
  {
    $date = $this->yesterday;

    $this->load->model('adminModel');
    $this->load->model('prizeModel');

    $user = $this->adminModel->pickWinner($date);

    //var_dump($user);

    if ($user == -1) {
      return $this->error($this->ERROR, 'No contest exists on ' . $date . '.');
    }
    elseif ($user == -2) {
      return $this->error($this->ERROR, 'We do not have any other entries on ' . $date . '.');
    }
    elseif (@$user['id'] >= 1) {
      // grab all of the information for this contest:
      $winner = $this->prizeModel->getWinnersByDateRange($date);
      if (!$winner) {
        return $this->error($this->ERROR, 'Winner picked, but then $this->getWinnersByDateRange(' . $date . ') failed.');
      }
      $winner = array_shift($winner);
      $this->sendMail($winner);
    }
    else{
      return $this->error($this->ERROR, 'Unexpected error from $this->adminModel->pickWinner(' . $date . ').');
    }
  }

  protected function error($msg, $status = 3)
  {
    $trace  = debug_backtrace();
    $caller = (@$trace[1]['class'] ? $trace[1]['class'] . '::' : '') . $trace[1]['function'];

    if ($status < 3) {
      $this->error_log .= '[' . date('Y-m-d H:i:s') . '] ' . $this->ERROR_STATUS_BY_INT[$status] . ' ' . $caller . PHP_EOL . $msg . PHP_EOL;
    }
    if ($this->debug) {
      print_r($msg);
    }
    if ($status == 1) {
      // FATAL
      exit;
    }
  }

  protected function sendMail($params)
  {
    $this->load->library('email');
    $this->load->library('parser');

    $params['date_pretty'] = date('F j, Y', strtotime($params['date']));

    // find correct "From:" in config/project.php:
    $froms = config_item('from');
    if (@$froms[$params['site_slug']]) {
      $from_email = $froms[$params['site_slug']]['email'];
      $from_name  = $froms[$params['site_slug']]['name'];
    }
    else {
      $from_email = $froms['default']['email'];
      $from_name  = $froms['default']['name'];
    }

    // winner email
    $this->email->from($from_email, $from_name);
    $this->email->to($params['user_email']);
    $this->email->bcc(config_item('admin_emails'));
    $this->email->subject($params['site_name'] . ' Winner Notification');

    switch ($params['prize_type']) {
      case "giftcard":
        $tpl = 'winner_giftcard';
        break;

      case "prize":

      default:
        $tpl = 'winner_prize';
    }

    // remove any HTML tags in the prize title
    $params['prize_title'] = safeTitle($params['prize_title']);

    $body = $this->parser->parse('../templates/' . $tpl, $params, true);
    $this->email->message($body);
    $this->email->send();
  }
}
