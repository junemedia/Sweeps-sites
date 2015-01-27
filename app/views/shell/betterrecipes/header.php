<!DOCTYPE html>
<head>
<?php if (@$meta['title']): ?>
<title><?= safeHtml($meta['title']) ?></title>
<?php endif; ?>
<?php if (@is_array($meta)) foreach ($meta as $key => $val): ?>
<meta name="<?= safeAttr($key) ?>" content="<?= safeAttr($val) ?>"/>
<?php endforeach; ?>
<?php
    $minify_config['noout'] = true;
    require_once('minify.inc.php');
    $minify_config['group'] = 'betterrecipes';
    new Minify($minify_config);
?>
</head>
<body id="jds" class="<?= $site_slug ?>">