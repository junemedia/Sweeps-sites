<? extract($data); ?>
    <div id="winners">
<? if (isset($winners)): ?>
        <h3>Recent Winners</h3>
        <div class="winners<?= !@$disable_slideshow ? ' slideshow' : '' ?>"><?
        $i = 0;
        foreach ($winners as $key => $winner):
            $img_tag = sprintf('<img %s="%s"/>', ($i++ >= 4 && !@$disable_slideshow) ? 'data-src' : 'src', $winner['prize_img1']);
            ?><div><?= $img_tag ?><div>
                    <h6><?= date("F j", strtotime($winner['date'])); ?></h6>
                    <h5><a href="/prize/<?= $winner['date'] ?>"><?= $winner['prize_title'] ?></a></h5>
                    <?= $winner['site_domain'] ?><br/><?= firstNameLastInitial($winner['user_firstname'], $winner['user_lastname']) ?><br/><?= $winner['user_city'] . ', ' . $winner['user_state'] ?></p>
                </div
            ></div><?
        endforeach;
        ?></div>
        <?php if (!@$disable_slideshow): ?>
        <a class="see_winners" href="/winners">See past winners</a>
        <?php endif; ?>
<? else: ?>
        <p>No winners yet.</p>
<? endif; ?>
    </div>