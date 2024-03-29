<? extract($data); ?>
<h1>Reset Your Password</h1>

<form id="reset_form" class="reset" action="/api/reset" method="POST">
    <fieldset>
    <label for="password">
    Please choose a new password:
    <input type="password" name="password"/>
    </label>
    </fieldset>
    <input type="hidden" name="token" value="<?= @$token ?>"/>
    <input type="submit" value="Reset Password"/>
    <p class="alert"></p>
    <p class="success">
    Your password has been updated.  Please return to the <a href="/">home page</a> and login with your new password.
    </p>
</form>