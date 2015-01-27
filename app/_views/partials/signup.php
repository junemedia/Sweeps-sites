<div id="signup" class="frame"><form id="signup_form" class="signup" method="post" action="/api/signup"><fieldset class="profile"><label for="firstname" class="req">First Name</label><input type="text" name="firstname" id="firstname"/><label for="lastname" class="req">Last Name</label><input type="text" name="lastname" id="firstname"/><label for="address" class="req">Address</label><input type="text" name="address" id="address"/><label for="zip" class="req">Zip Code</label><input type="text" name="zip" id="zip"/></fieldset><fieldset class="credentials hide_on_update"><label for="email" class="req">Email</label><input type="text" name="email" id="email"/><label for="password" class="req">Password</label><input type="password" name="password" id="password"/></fieldset><div class="alert"></div><fieldset class="submit"><input type="submit" value="Next"/><span class="ajax-loader"></span></fieldset></form><form id="login_form" class="login" method="post" action="/api/login"><h2>Already a Member?</h2><fieldset class="login"><label for="login_email">Email</label><input type="text" name="email" id="login_email"/><label for="login_password">Password</label><input name="password" type="password" id="login_password"/></fieldset><a class="forgot">Forgot password?</a><div class="alert"></div><fieldset class="submit"><input type="submit" value="Enter Now"/><span class="ajax-loader"></span></fieldset></form><form id="forgot_form" class="login" method="post" action="/api/forgot"><h2>Forget your password?</h2><p>Enter your email address and we’ll send you a message with instructions how to reset your password.</p><fieldset class="login"><label for="forgot_email">Email</label><input type="text" name="email" id="forgot_email"/></fieldset><div class="alert"></div><fieldset class="submit"><input type="submit" value="Submit"/><span class="ajax-loader"></span><a class="forgot_close">Cancel</a></fieldset></form></div>