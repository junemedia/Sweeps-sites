<div id="prize" class="barrel">

    <? $this->load->view('partials/banner/home'); ?>

<? if (@$prize): ?>
    <? if (@$prev): ?>
        <? if (@$prev['date'] == date('Y-m-d')): ?>
        <a class="prev" href="<?= $channel_url ?>">&lt;&nbsp;Today’s Prize</a>
        <? else: ?>
        <a class="prev" href="<?= $channel_url ?>prize/<?= $prev['date'] ?>">&lt;&nbsp;<?= date('F j', strtotime($prev['date'])) ?></a>
        <? endif; ?>
    <? endif; ?>
    <? if (@$next): ?>
        <? if (@$next['date'] == date('Y-m-d')): ?>
        <a class="next" href="<?= $channel_url ?>">Today’s Prize&nbsp;&gt;</a>
        <? else: ?>
        <a class="next" href="<?= $channel_url ?>prize/<?= $next['date'] ?>"><?= date('F j', strtotime($next['date'])) ?>&nbsp;&gt;</a>
        <? endif; ?>
    <? endif; ?>
    <h3 class="prize_today"><?= date('Y') == date('Y', strtotime($prize['date'])) ? date( "F j", strtotime($prize['date'])) : date( "F j, Y", strtotime($prize['date'])); ?></h3>
    <div class="prize col2"><!-- NO GAP
     --><img src="<?= $prize['image'] ?>"/><!-- NO GAP
     --><div class="info">
            <h1><?= $prize['title'] ?></h1>
            <p><?= $prize['description'] ?></p>
            <p class="directives">
                <? if ($prize['url']): ?><a href="<?= $prize['url'] ?>" target="_blank">More about this prize</a><? endif; ?><!-- NO GAP
             --><a href="<?= $channel_url ?>rules" target="_blank">Official Rules</a>
            </p>
        </div><!-- NO GAP
 --></div>
<? else: ?>
    <h3>No prize for this date.</h3>
<? endif; ?>

</div>