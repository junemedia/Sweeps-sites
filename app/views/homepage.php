<?
$this->load->view('shell/'.$site_slug.'/header', compact($site_slug, $meta));

$this->load->view('partials/prize', array('prize' => $today));
$this->load->view('partials/signup', compact($winners));
$this->load->view('partials/thankyou', compact($tomorrow));
$this->load->view('partials/carousel', compact($today));
$this->load->view('partials/winners', compact($winners));

$this->load->view('shell/'.$site_slug.'/footer', compact($solvemedia));
?>