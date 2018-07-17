<h2>Login</h2>
<p>Please fill in your credentials to login.</p>
<?php if( !empty( $login_err ) ): ?>
<p><?php echo $login_err; ?></p>
<?php endif; ?>
<form action="/login" method="post">
	<div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
		<label>Username</label>
		<input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
		<span class="help-block"><?php echo $username_err; ?></span>
	</div>    
	<div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
		<label>Password</label>
		<input type="password" name="password" class="form-control">
		<span class="help-block"><?php echo $password_err; ?></span>
	</div>
	<div class="form-group">
		<input type="submit" class="btn btn-primary" value="Login">
	</div>
	<p>Don't have an account? <a href="/sign-up">Sign up now</a>.</p>
</form>

