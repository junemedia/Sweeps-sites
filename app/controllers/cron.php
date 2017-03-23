<?php

class Cron extends CI_Controller
{
  protected $debug               = true;
  protected $log                 = '';
  protected $log_email           = null;
  protected $log_dir             = '/srv/sites/dailysweeps/bin/logs';
  protected $log_file            = '';
  protected $INFO                = 4;
  protected $WARN                = 3;
  protected $ERROR               = 2;
  protected $FATAL               = 1;
  protected $ERROR_STATUS_BY_INT = array(
                                     1 => 'FATAL',
                                     2 => 'ERROR',
                                     3 => 'WARN',
                                     4 => 'INFO',
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
    $this->log_file  = $this->log_dir . '/' . $this->today . '.log';
  }

  public function __destruct()
  {
    if (!$this->log) {
      return;
    }
    $this->log .= "\n************************************************\n\n";
    @file_put_contents($this->log_file, $this->log, FILE_APPEND);
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
    /*
     * possible values for $user:
     *   -1: no contest set for this $date
     *   -2: no eligible entries for this $date
     *   array of user data: `id`, `firstname`, `lastname`, `email`, `city`, `state`
     */
    if ($user == -1) {
      return $this->logItem($this->ERROR, 'No contest exists on ' . $date . '.');
    }
    elseif ($user == -2) {
      return $this->logItem($this->ERROR, 'We do not have any other entries on ' . $date . '.');
    }
    elseif (@$user['id'] >= 1) {
      $this->logItem($this->INFO, 'Winner id: ' . $user['id']);
      $this->logItem($this->INFO, 'Winner email: ' . $user['email']);

      // grab all of the information for this contest:
      $winner = $this->prizeModel->getWinnersByDateRange($date);
      if (!$winner) {
        return $this->logItem($this->ERROR, 'Winner picked, but then $this->getWinnersByDateRange(' . $date . ') failed.');
      }
      $winner = array_shift($winner);
      $this->logItem($this->INFO, print_r($winner, true));

      $this->sendMail($winner);
    }
    else {
      return $this->logItem($this->ERROR, 'Unexpected error from $this->adminModel->pickWinner(' . $date . ').');
    }
  }

  protected function logItem($status = 3, $msg)
  {
    $trace  = debug_backtrace();
    $caller = (@$trace[1]['class'] ? $trace[1]['class'] . '::' : '') . $trace[1]['function'];

    if ($status < 3 || $this->debug) {
      $this->log .= '[' . date('Y-m-d H:i:s') . '] ' . $this->ERROR_STATUS_BY_INT[$status] . ' ' . $caller . PHP_EOL . $msg . PHP_EOL;
    }
    if ($status == 1) {
      // FATAL
      exit;
    }
  }

  protected function sendMail($params)
  {
    $this->load->library('maropost', config_item('maropost'));

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
    $this->maropost->from(array(
      'email'    => $from_email,
      'name'     => $from_name,
      'reply_to' => config_item('admin_email')
    ));

    $this->maropost->to($params['user_email']);
    $this->maropost->bcc(config_item('admin_email'));
    $this->maropost->subject($params['site_name'] . ' Winner Notification');

    // remove any HTML tags in the prize title
    $params['prize_title'] = safeTitle($params['prize_title']);

    // tags
    $this->maropost->tags('winner_email', $params['user_email']);
    $this->maropost->tags('prize_title', $params['prize_title']);
    $this->maropost->tags('prize_value', $params['prize_value']);
    $this->maropost->tags('prize_date', $params['date_pretty']);
    $this->maropost->tags('site_name', $params['site_name']);
    $this->maropost->tags('site_domain', $params['site_domain']);

    $response = $this->maropost->send_transaction();

    $recd = $response['recd'];
    $postfields = print_r($response['sent']['postfields'], true);
    $info = print_r($response['sent']['info'], true);

    $this->logItem($this->INFO, "POST data: $postfields");
    $this->logItem($this->INFO, "cURL info: $info");
    $this->logItem($this->INFO, "cURL response: $recd");
  }
}
