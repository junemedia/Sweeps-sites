<? extract($data) ?>
<a href="/admin/prize/0">New Prize</a>
<table>
<?php foreach($contests as $contest):
    extract($contest);
    if ($user_id) {
        $winner_html = sprintf('%s %s &lt;<a href="mailto:%s">%s</a>&gt;', $user_firstname, $user_lastname, $user_email, $user_email);
    } else {
        $winner_html = '-';
    }
?>
    <tr>
        <td><?= $date ?></td>
        <td><a href="/admin/prize/<?= $prize_id ?>#<?= $date ?>"><?= $prize_title ?></a></td>
        <td><?= '$' . $prize_award . ' Gift Card' . ($prize_type == 'prize' ? ' or Prize' : '') ?></td>
        <td><?= $winner_html ?></td>
    </tr>
<?php endforeach; ?>
</table>
<h3>WE NEED TO SHOW PRIZES WITHOUT ANY DATES SOMEWHERE HERE</h3>