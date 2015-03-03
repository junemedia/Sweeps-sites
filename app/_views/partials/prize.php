<?php extract($data); $is_todays_prize = date('Y-m-d') == $prize['date']; $tag_img = @$prize['img1'] ? sprintf('<img%s%s%s/>', ' src="' . $prize['img1'] . '"', @$prize['img2'] ? ' data-img2="' . $prize['img2'] .'"' : '', @$prize['img3'] ? ' data-img3="' . $prize['img3'] .'"' : '' ) : ''; $tag_desc = @$prize['desc1'] ? sprintf('<p%s%s>%s</p>', @$prize['desc2'] ? ' data-desc2="' . safeAttr($prize['desc2']) . '"' : '', @$prize['desc3'] ? ' data-desc3="' . safeAttr($prize['desc3']) . '"' : '', safeHtml($prize['desc1']) ) : ''; ?><div id="prize" class="frame"><? if (@$prize): ?><h3 class="account">Welcome <a href="/profile"></a>, <a class="logout">logout</a></h3><h3 class="prize_today"><?= date('Y') == date('Y', strtotime($prize['date'])) ? date( "F j", strtotime($prize['date'])) : date( "F j, Y", strtotime($prize['date'])); ?><? if ($is_todays_prize) echo '&nbsp;| <span>Win today’s prize</span>'; ?></h3><div class="prize"><?= $tag_img ?><div class="info"><h1><?= $prize['title'] ?></h1><?= $tag_desc ?><div class="alert"></div><?php if ($is_todays_prize): ?><form id="prize_form" class="submit" action="/api/enter" method="POST"><input type="submit" value="Enter Now"/><span class="loader"></span></form><?php else: ?><a href="/">Back to today’s prize</a><?php endif ?><p class="legal">Prizes are shared across June Media sites. <span>See <a href="/rules">Official Rules</a>.</span></p></div></div><? else: ?><h3>No prize for this date.</h3><? endif; ?></div>