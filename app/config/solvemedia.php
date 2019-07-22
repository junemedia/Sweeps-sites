<?php

/*
|--------------------------------------------------------------------------
| SolveMedia Keys
|--------------------------------------------------------------------------
|
| Choose the correct key set based on HTTP_HOST since this is only used
| through php-fpm.
|
 */

switch (@$_SERVER['HTTP_HOST']) {

  case 'stagewin.betterrecipes.com':
  case 'win.betterrecipes.com':
    $config['public']  = 'ym7RhIOhnKDH44Vt.atFOnHyicq2FVs6';
    $config['private'] = '-ki9FiWGRSsHjl-QV5k-d2qML43hfHwQ';
    $config['verify']  = 'fJCQQ3WPIPZCX26rqBOyM5v41q2lKQRt';
    break;

  case 'stagewin.recipe4living.com':
  case 'win.recipe4living.com':
    $config['public']  = 'JvXkbYXwE-M0eL86F6xVTQB0OHhmEyXU';
    $config['private'] = 'JfQ5pkUNZslz3vU.7nDjGP3EcGxcESg.';
    $config['verify']  = 'GdMrgUips.13Bx-BsSm5INFAOjz0tpMt';
    break;
}
