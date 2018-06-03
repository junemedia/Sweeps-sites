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


  /**
   * Cleans reset tokens from the database.
   *
   * @return void
   */
  public function clean_tokens()
  {

    $this->load->model('adminModel');

    $result = $this->adminModel->clearResetTokens();
    $logdata = $result ? 'true' : 'false';

    $this->logItem($this->INFO, 'clearResetTokens: ' . $logdata);
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
    $this->load->library('emarsys', config_item('emarsys'));

    $params['date_pretty'] = date('F j, Y', strtotime($params['date']));
    // remove any HTML tags in the prize title
    $params['prize_title'] = safeTitle($params['prize_title']);

    $payload = (object)[
      'key_id'      => '3',
      'external_id' => $params['user_email'],
      'data'        => (object)[
        'winner_email' => $params['user_email'],
        'prize_title'  => $params['prize_title'],
        'prize_value'  => $params['prize_value'],
        'prize_date'   => $params['date_pretty'],
        'site_name'    => $params['site_name'],
        'site_domain'  => $params['site_domain']
      ]
    ];

    $response = $this->emarsys->post('event/1262/trigger', json_encode($payload));

    $recd       = $response['recd'];
    $postfields = print_r($response['sent']['postfields'], true);
    $info       = print_r($response['sent']['info'], true);

    $this->logItem($this->INFO, "POST data: $postfields");
    $this->logItem($this->INFO, "cURL info: $info");
    $this->logItem($this->INFO, "cURL response: $recd");
  }
}
