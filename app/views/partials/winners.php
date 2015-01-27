    <div class="winners">
<? if (isset($winners)): ?>
        <h3>Recent Winners</h3>
        <p>Winners and prizes are site specific</p>
        <div class="list col2"><?
        foreach ($winners as $key => $winner):
            ?><div class="winner col2"><img
            src="<?= trim($winner['prize_img1']) ?>"/><div>
                    <h6 class="day"><?= date( "F j", strtotime($winner['date'])); ?></h6>
                    <h5><a><?= trim($winner['prize_title']) ?></a></h5>
                    <?= $winner['user_firstname'] ?><br/><?= $winner['user_city'] ?></p>
                </div
            ></div><?
        endforeach;
        ?></div>
        <a class="see_winners" href="/winners">See Past Winners</a>
<? else: ?>
        <p>No winners yet.</p>
<? endif; ?>
    </div>