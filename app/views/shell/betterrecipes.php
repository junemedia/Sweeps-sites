<? extract($data); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?= safeHtml(safeTitle(@$meta['title'] ? $meta['title'] : @$meta['og:title'])) ?></title>
  <meta name="viewport" content="width=device-width"/>
  <meta name="description" content="<?= safeAttr(safeTitle(@$meta['description'] ? $meta['description'] : @$meta['og:description'])) ?>"/>
  <?php if (@is_array($meta)) foreach ($meta as $key => $val): ?>
  <meta name="<?= safeAttr($key) ?>" content="<?= safeAttr(safeTitle($val)) ?>"/>
  <?php endforeach; ?>
  <link rel="stylesheet" href="<?= $assets['/css/betterrecipes.css'] ?>"/>
  <link rel="shortcut icon" href="http://www.betterrecipes.com/favicon.ico"/>

  <script type="text/javascript">
    function solvemedia()
    {
      ACPuzzle.create('ym7RhIOhnKDH44Vt.atFOnHyicq2FVs6', 'acwidget', { size: 'standard' });
    }

    function closeSolve()
    {
      ACPuzzle.destroy();
      $("#solvemedia").hide();
    }
  </script>
</head>

<body>
  <header>
      <div>
          <a href="http://www.betterrecipes.com/" class="logo">BetterRecipes</a>
          <div class="ad" data-id="728x90_ATF"></div>
          <a class="menu"></a>
      </div>
      <nav><!-- NO GAP
        --><div><!-- NO GAP
            --><a href="http://www.betterrecipes.com/">Home</a><!-- NO GAP
            --><a href="http://www.betterrecipes.com/recipes">Recipes</a><!-- NO GAP
            --><a href="http://www.betterrecipes.com/blogs/daily-dish">The Daily Dish</a><!-- NO GAP
            --><a href="http://win.betterrecipes.com/" class="on">Sweepstakes</a><!-- NO GAP
            --><form method="GET" action="http://www.betterrecipes.com/search"><!-- NO GAP
                --><input type="text" name="term" placeholder="Search for recipe" required/><!-- NO GAP
                --><input type="submit" value="Search"/><!-- NO GAP
                --><input type="hidden" name="PageType" value="Recipe"/><!-- NO GAP
            --></form><!-- NO GAP
        --></div><!-- NO GAP
    --></nav>
  </header>

  <div id="jds" class="<?= $site_slug ?>">
      <div class="main">
      <!-- THIS SHOULD EVENTUALLY BE PART OF THE ADMIN -->
      <a class="banner" href="/"><span>Enter to win prizes daily</span></a>

      <?php
        foreach ($view as $v) {
          $this->load->view($v, compact('data'));
        }
      ?>

      </div><!-- NO GAP
      --><div class="rail">
        <!-- Right Rail Ad Units -->
        <div class="ad" data-id="300x250_ATF"></div>
        <div class="ad" data-id="ourbestbox"></div>
        <div class="ad" data-id="300x250_BTF"></div>
        <div class="ad" data-id="zergnet-widget-29457"></div>
      </div>
  </div>

  <div class="ad" data-id="728x90_BTF"></div>

  <footer>
    <div>
      <!-- Dynamic footer links will be inserted here -->
      <nav>
        <a>©<?= ' '.date('Y').' ' ?>June Media Inc</a>
        <a href="http://www.betterrecipes.com/privacy-policy">Privacy Policy</a>
        <a href="http://www.betterrecipes.com/terms">Terms of Service</a>
      </nav>
    </div>
  </footer>

  <?php
    if (@$solvemedia) {
      $this->load->view('partials/captcha');
    }
  ?>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <!-- Create the `jds` pre-object/function which allows for jds() calls before jds.js is loaded -->
  <script>(function(w,m){w[m]=w[m]&&!w[m].nodeName?w[m]:function(){(w[m].q=w[m].q||[]).push(arguments)}})(window,'jds')<?php

      $js_script_arr = array();

      /* SolveMedia */
      if (@$solvemedia) {
          $js_script_arr[] = $solvemedia;
      }

      /* GTM */
      if (@$site_gtm) {
          $js_script_arr[] = 'jds("gtm","' . $site_gtm . '")';
      }

      if ($js_script_arr) {
          echo ';' . implode(';', $js_script_arr);
      }
?></script>
  <script async src="<?= $assets['/js/betterrecipes.js'] ?>"></script>

  <script>
    $(document).ready(function(){
      $.ajaxSetup({
        cache: true
      });

      $.getScript( "http://api.solvemedia.com/papi/challenge.ajax", function( data, textStatus, jqxhr ) {
        //console.log( data ); // Data returned
      });
    });
  </script>

  <?php $this->load->view("ads/$site_slug/underdog"); ?>
  <?php $this->load->view("ads/$site_slug/swoop"); ?>
  <?php $this->load->view("ads/$site_slug/liveconnect"); ?>

</body>
</html>
