<?php
    $data['disable_slideshow'] = true;
    extract($data);
?>
<?php $this->load->view('partials/winners', $data); ?>