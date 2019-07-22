<?php

/**
 * Sweeps Controller
 *
 * @author    Adam Chalemian [achalemian@resolute.com]
 * @copyright 2015 Resolute Digital [http://resolute.com]
 */
class SweepsController extends CI_Controller
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Output a JSON response with the correct Content-Type header
     *
     * @param   integer $code   status code
     * @param   mixed   $data   array, string, or object to be encoded
     * @return void
     */
    protected function json($code = 1, $data = null)
    {
        if (is_array($data)) {
            $data['status'] = $code;
        } else if (is_object($data)) {
            $data->status = $code;
        } else {
            $data = array('status' => $code, 'message' => $data);
        }

        // do not do an array_filter here, because code=0’s will be removed.
        $this->output
             ->set_content_type('application/json; charset=UTF-8')
             ->set_output($this->jsonEncode($data));
    }

    /**
     * Sanitize any response array before applying json_encode()
     *
     * @param   mixed   $data
     * @return  json
     */
    protected function jsonEncode($data)
    {
        if (!$data || !is_array($data)) {
            return is_array($data) ? '[]' : '{}';
        }
        return json_encode($this->removeNullEmpty($data), JSON_NUMERIC_CHECK);
    }

    /**
     * Remove empty or null values from a response array
     *
     * @param   array   $data
     * @return  array   With $data cleaned recursively
     */
    protected function removeNullEmpty($data)
    {
        foreach ($data as &$val) {
            if (is_array($val)) {
                $val = $this->removeNullEmpty($val);
            }
        }
        return array_filter($data, function ($v) {return !($v === array() || $v === "" || $v === null);});
    }

    /**
     * Return an array of the etc/assets.json
     *
     * @return  array
     */
    protected function assets()
    {
        return json_decode(file_get_contents(APPPATH . '../etc/assets.json'), true);
    }

}

class FrontendController extends SweepsController
{
    protected $site_id     = null;
    protected $site_slug   = null;
    protected $site_name   = null;
    protected $site_domain = null;
    protected $site_gtm    = null;

    public function __construct()
    {
        parent::__construct();

        $this->db->select('id as site_id, slug as site_slug, name as site_name, domain as site_domain, gtm as site_gtm');

        // production (win.) domains are easy to match against the `site`.`domain` column
        if (strpos($_SERVER['HTTP_HOST'], 'win.') === 0) {
            $this->db->where('domain', $_SERVER['HTTP_HOST']);
        }
        else {
            // development/staging domains must have the `slug` in the format stagewin.SLUG.com
            if (!preg_match('/^stagewin\.(recipe4living|betterrecipes)\.com/', $_SERVER['HTTP_HOST'], $m)) {
                show_404();
            }
            $this->db->where('slug', $m[1]);
        }

        $this->db->limit(1);

        $response = $this->db->get('site')->row_array();

        if (!$response) {
            show_404();
        }

        // assigned each of these fields (columns) to $this object
        foreach ($response as $key => $value) {
            $this->{$key} = $value;
        }

    }

    /**
     * Wrap the load->view() CI method
     *
     * @param   mixed   string or array of views to load in order
     * @param   mixed   data to pass to view
     *
     * @return  void
     */
    public function loadView($view, $data)
    {
        // load some basics for all views
        $data['site_id']     = $this->site_id;
        $data['site_slug']   = $this->site_slug;
        $data['site_name']   = $this->site_name;
        $data['site_domain'] = $this->site_domain;
        $data['site_gtm']    = $this->site_gtm;

        // assign the views
        $data['view'] = is_array($view) ? $view : array($view);

        // assign the $assets
        $data['assets'] = $this->assets();

        return $this->load->view('shell/' . $this->site_slug . '.php', compact('data'));
    }

    /**
     * Are we human?  Have we correctly answered a capture since
     * app/config/project.php: $config['human_ttl'] seconds?
     *
     * @return  boolean
     */
    public function isHuman($set = null)
    {
        // is this a set?
        if ($set === true) {
            $this->session->set_userdata('human', time());
            return true;
        } elseif ($set === false) {
            $this->session->set_userdata('human', null);
            return false;
        }

        if (!@$this->human_ttl) {
            // load up the configurable TTL for the captcha
            $this->human_ttl = config_item('human_ttl');
        }

        $human = $this->session->userdata('human');
        if (!$human) {
            return false;
        }
        if (time() - $human > $this->human_ttl) {
            $this->session->set_userdata('human', null); // zero out for good measure
            return false;
        }

        // otherwise, we're still considered human
        return true;
    }
}

class AdminController extends SweepsController
{
    public function __construct()
    {
        parent::__construct();

        // only load the Session library for this controller and all of its actions
        $this->load->library('session');

        // all responses here should never be cached
        $this->rdcache->expires(-1);

        // if we're not an admin, bail
        if (!$this->session->userdata('is_admin')) {
            show_404();
        }
    }

    /**
     * Wrap the load->view() CI method
     *
     * @param   mixed   string or array of views to load in order
     * @param   mixed   data to pass to view
     *
     * @return  void
     */
    public function loadView($view, $data)
    {
        // assign the views
        $data['view'] = is_array($view) ? $view : array($view);

        // assign the $assets
        $data['assets'] = $this->assets();

        return $this->load->view('admin/index', compact('data'));
    }
}
