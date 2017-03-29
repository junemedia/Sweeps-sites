<?php

class AdminModel extends CI_Model
{

  /**
   * Get prizes not associated with any contest dates
   *
   * @param   integer $prize_id
   *
   * @return  array
   */
  public function getEmptyPrizes()
  {
    return $this->db
                ->get('view_prize_empty')
                ->result_array();
  }

  /**
   * Get a prize by id
   *
   * @param   integer $prize_id
   *
   * @return  array
   */
  public function getPrize($prize_id)
  {
    return $this->db
                ->where('id', $prize_id)
                ->get('view_prize_admin')
                ->row_array();
  }

  /**
   * Create a prize
   *
   * @param   array   $prize
   *
   * @return  array
   */
  public function createPrize($prize)
  {
    // For SPROCs, you MUST use $query->free_result() to avoid
    // getting the "2014 Commands out of sync" mysql error.
    $sql = sprintf('CALL CREATE_PRIZE(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)',
                   $this->db->escape(@$prize['title']),
                   $this->db->escape(@$prize['img1']),
                   $this->db->escape(@$prize['desc1']),
                   $this->db->escape(@$prize['img2']),
                   $this->db->escape(@$prize['desc2']),
                   $this->db->escape(@$prize['img3']),
                   $this->db->escape(@$prize['desc3']),
                   $this->db->escape(@$prize['award']),
                   $this->db->escape(@$prize['value']),
                   $this->db->escape(@$prize['type']));
    $query  = $this->db->query($sql);
    $result = $query->row_array();
    $query->free_result();

    return (int) $result['prize_id'];
  }

  /**
   * Update a prize
   *
   * @param   array   $prize
   *
   * @return  array
   */
  public function updatePrize($prize_id, $prize)
  {
    // For SPROCs, you MUST use $query->free_result() to avoid
    // getting the "2014 Commands out of sync" mysql error.
    $sql = sprintf('CALL UPDATE_PRIZE(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)',
                   $this->db->escape(@$prize_id),
                   $this->db->escape(@$prize['title']),
                   $this->db->escape(@$prize['img1']),
                   $this->db->escape(@$prize['desc1']),
                   $this->db->escape(@$prize['img2']),
                   $this->db->escape(@$prize['desc2']),
                   $this->db->escape(@$prize['img3']),
                   $this->db->escape(@$prize['desc3']),
                   $this->db->escape(@$prize['award']),
                   $this->db->escape(@$prize['value']),
                   $this->db->escape(@$prize['type']));
    $query  = $this->db->query($sql);
    $result = $query->row_array();
    $query->free_result();

    return (int) $result['success'];
  }

  /**
   * Very similar to getWinnersByDateRange(), except that we do not exclude contests without winners
   *
   * @param   date    $begin_date (YYYY-MM-DD)
   * @param   date    $end_date   (YYYY-MM-DD optional, default today)
   *
   * @return array    array of contest objects including winner information
   */
  public function getContestsByDateRange($begin_date, $end_date = null)
  {
    if (!$end_date) {
      $end_date = date('Y-m-d');
    }
    if (!$begin_date) {
      $begin_date = $end_date;
    }
    return $this->db
                ->where(sprintf('`date` BETWEEN "%s" AND "%s"', $begin_date, $end_date), null, false)
                ->order_by('date DESC')
                ->get('view_contest')
                ->result_array();
  }

  /**
   * Sortable, filterable
   *
   * @param   integer $offset
   * @param   integer $limit
   * @param   boolean $reverse the default date desc sorting
   * @param   string  $query to match LIKE %$query% against prize title
   *
   * @return array    array of contest objects including winner information
   */
  public function getContestsWithFilters($offset = 0, $limit = 100, $reverse = false, $query = '')
  {
    if ($query) {
      $this->db->where('`prize_title` LIKE "%' . $this->db->escape_str($query) . '%"', null, false);
    }

    if (!$reverse) {
      $this->db->order_by('date DESC');
    }
    else {
      $this->db->order_by('date ASC');
    }

    return $this->db
                ->limit($limit, $offset)
                ->get('view_contest')
                ->result_array();
  }

  /**
   * Get contests (flight dates) for a given prize
   *
   * @param   integer $prize_id
   *
   * @return  array   array of contest objects including winner information
   */
  public function getContestsByPrizeId($prize_id)
  {
    return $this->db
                ->where('prize_id', $prize_id)
                ->order_by('date ASC')
                ->get('view_contest')
                ->result_array();
  }

  /**
   * Get only contest dates for a given prize; used for purging cache
   *
   * @param   integer $prize_id
   *
   * @return  array   array of contest dates for the prize
   */
  public function getContestDatesByPrizeId($prize_id)
  {
    return $this->db
                ->select('date')
                ->where('prize_id', $prize_id)
                ->get('contest')
                ->result_array();
  }

  /**
   * Add a contest (flight date) to a given prize
   *
   * @param   integer $prize_id
   * @param   string  $date
   *
   * @return  boolean
   */
  public function addContest($prize_id, $date)
  {
    $this->db
         ->insert('contest', array('prize_id' => $prize_id, 'date' => $date));
    return $this->db->affected_rows() == 1;
  }

  /**
   * Remove a contest (flight date) from a given prize
   *
   * @param   integer $prize_id
   * @param   string  $date
   *
   * @return  boolean
   */
  public function delContest($prize_id, $date)
  {
    $this->db
         ->where(array('prize_id' => $prize_id, 'date' => $date))
         ->delete('contest');
    return $this->db->affected_rows() == 1;
  }

  /**
   * Pick a winner for a given contest date
   *
   * You must make sure that the following is satisfied in your controller:
   *  1)  Contest (date) is in the future or is still running today
   *
   * Possible scenarios:
   *  1)  Contest (date) does not exist
   *  2)  Contest (date) does not have any entries
   *
   * @param   date    $date
   *
   * @return  mixed   $winner object or integer corresponding
   *                  to scenarios listed above
   */
  public function pickWinner($date)
  {

    // For SPROCs, you MUST use $query->free_result() to avoid
    // getting the "2014 Commands out of sync" mysql error.
    $sql = sprintf('CALL PICK_WINNER(%s)',
                   $this->db->escape(@$date));
    $query  = $this->db->query($sql);
    $result = $query->row_array();
    $query->free_result();

    return @$result['error'] ? $result['error'] : $result;
  }

  /**
   * Return all `site` rows including the `thanks` column
   *
   * @param   integer $prize_id
   * @param   string  $date
   *
   * @return  boolean
   */
  public function getSites()
  {
    return $this->db
                ->get('site')
                ->result_array();
  }

  /**
   * Update a `site` with new thank you HTML
   *
   *
   * THIS SHOULD BE A SPROC
   * THIS SHOULD BE A SPROC
   * THIS SHOULD BE A SPROC
   * THIS SHOULD BE A SPROC
   * THIS SHOULD BE A SPROC
   * THIS SHOULD BE A SPROC
   *
   *
   * @param   integer $site_id
   * @param   string  $html
   *
   * @return  boolean
   */
  public function setThanks($site_id, $html)
  {
    $sql = sprintf('UPDATE `site` SET `thanks`=%s WHERE `id`=%d',
                   ($html ? $this->db->escape($html) : 'NULL'),
                   $site_id);
    return $this->db->query($sql);
  }

  /**
   * Clean expired 'reset' tokens from `reset`
   *
   * @return  boolean
   */
  public function clearResetTokens()
  {
    $expiration = date('Y-m-d H:i:s', strtotime('-86400 seconds'));
    return $this->db
                ->where('type', 'reset')
                ->where('timestamp <', $expiration)
                ->delete('reset');
  }

}
