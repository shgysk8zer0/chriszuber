<?php
	$formname = filename(__FILE__);
?>
<dialog id="<?=$formname?>_dialog" open>
	<form name="<?=$formname?>" action="<?=URL?>/" method="post">
		<details open>
			<summary>Server Database Connection Credentials</summary>
			<fieldset form="<?=$formname?>">
				<legend>Root MySQL User</legend>
				<label for="<?=$formname?>_root_useer" data-icon="U"></label>
				<input type="text" name="<?=$formname?>[root][user]" id="<?=$formname?>_root_user" placeholder="Root MySQL User" pattern="\w+" required/><br >
				<label for="<?=$formname?>_root_password" data-icon="x"></label>
				<input type="password" name="<?=$formname?>[root][password]" id="<?=$formname?>_root_password" placeholder="Root MySQL Password"/>
			</fieldset>
			</details>
		<?php if(!file_exists(BASE . '/config/connect.ini')):?>
			<?php if(!is_readable(BASE . '/config/')):?>
			<strong><code>config/</code> directory cannot be written to.</strong>
			<p>
				Please change permissions using
				<pre><code>
	chmod -R g+w config
	chgrp -R www-data config
				</code></pre>
			or manually create <code>config/connect.ini</code> using
			<pre><code>
	user = "{username}"
	password = "{password}"
			</code></pre>
			</p>
			<?php else:?>
			<details>
				<summary>Database Connection Settings</summary>
				<fieldset form="<?=$formname?>">
					<legend>Database Connection Settings</legend>
					<label for="<?=$formname?>_connect_user" data-icon="U"></label>
					<input type="text" name="<?=$formname?>[connect][user]" id="<?=$formname?>_connect_user" placeholder="Username" pattern="\w{5,}" required/><br />
					<label for="<?=$formname?>_connect_password" data-icon="x"></label>
					<input type="password" name="<?=$formname?>[connect][password]" id="<?=$formname?>_connect_password" placeholder="Password" pattern="<?=pattern('password')?>" required/><br />
					<label for="<?=$formname?>_connect_repeat" data-icon="*"></label>
					<input type="password" name="<?=$formname?>[connect][repeat]" id="<?=$formname?>_connect_repeat" data-must-match="<?=$formname?>[connect][password]" placeholder="Repeat Password" required/>
				</fieldset>
			</details>
			<?php endif?>
		<?php endif?>
		<details>
			<summary>User Login</summary>
			<fieldset form="<?=$formname?>">
				<legend>Your Login for the site</legend>
					<label for="<?=$formname?>_site_user" data-icon="@" title="Username must be an email address"></label>
					<input type="email" name="<?=$formname?>[site][user]" id="<?=$formname?>_site_user" placeholder="user@example.com" required/><br />
					<label for="<?=$formname?>_site_password" data-icon="x"></label>
					<input type="password" name="<?=$formname?>[site][password]" id="<?=$formname?>_site_password" placeholder="Password" pattern="<?=pattern('password')?>" required/><br />
					<label for="<?=$formname?>_site_repeat" data-icon="*"></label>
					<input type="password" name="<?=$formname?>[site][repeat]" id="<?=$formname?>_site_repeat" placeholder="Repeat password" data-must-match="<?=$formname?>[site][password]" required/>
			</fieldset>
		</details>
		<details>
			<summary>Basic Site Info</summary>
			<fieldset form="<?=$formname?>">
				<label for="<?=$formname?>_head_title">Title</label>
				<input type="text" name="<?=$formname?>[head][title]" id="<?=$formname?>_head_title" value="<?=ucwords(str_replace(['-', '_'], ' ', end(explode('/', trim(URL, '/')))))?>" placeholder="Site Title" pattern="[\w- ]{5,}" required/><br />
				<label for="<?=$formname?>_head_description">Description</label><br />
				<textarea name="<?=$formname?>[head][description]" id="<?=$formname?>_head_description" placeholder="Description will appear in searches. 160 character limit" maxlength="160" required></textarea><br />
				<label for="<?=$formname?>_head_keywords">Keywords</label>
				<input type="text" name="<?=$formname?>[head][keywords]" id="<?=$formname?>_head_keywords" placeholder="Comma separated keywords" pattern="[\w, -]+" required/><br />
				<b>Allow search engines to scan and index this site?</b>
				<input type="radio" name="<?=$formname?>[head][robots]" id="<?=$formname?>_head_robots" value="follow, index" checked/>
				<label for="<?=$formname?>_head_robots">Yes</label>
				<input type="radio" name="<?=$formname?>[head][robots]" id="<?=$formname?>_head_robots" value="nofollow, noindex" checked/>
				<label for="<?=$formname?>_head_robots">No</label><br />
				<label for="<?=$formname?>_head_ga">Google Analytics Code</label>
				<input type="text" name="<?=$formname?>[head][google_analytics_code]" id="<?=$formname?>_head_ga" maxlength="13" size="13" pattern="[A-z]{2}-[A-z\d]{8}-\d" placeholder="XX-XXXXXXXX-XX"/><br />
				<label for="<?=$formname?>_head_author">Author Name</label>
				<input type="text" name="<?=$formname?>[head][author]" id="<?=$formname?>_head_author" pattern="[\w- ]{5,}" placeholder="Clark Kent"/><br />
				<label for="<?=$formname?>_head_author_g_pluss">Author Google Plus</label>
				<input type="url" name="<?=$formname?>[head][author_g_plus]" id="<?=$formname?>_head_author_g_plus" size="37" placeholder="https://plus.google.com/+{profile id}"/><br />
				<label for="<?=$formname?>_head_publisher">Google Plus Page</label>
				<input type="url" name="<?=$formname?>[head][publisher]" id="<?=$formname?>_publisher" size="37" placeholder="https://plus.google.com/+{profile id}"/><br />
				<label for="<?=$formname?>_head_rss">RSS URL</label>
				<input type="url" name="<?=$formname?>[head][rss]" id="<?=$formname?>_head_rss" placeholder="<?=URL?>/feeds.rss"/><br />
				<input type="hidden" name="<?=$formname?>[head][viewport]" value="width=device-width, height=device-height" required readonly/>
				<input type="hidden" name="<?=$formname?>[head][charset]" value="utf-8" required readonly/>
			</fieldset>
		</details>
		<button type="submit" data-icon="." title="Submit"></button>
		<button type="reset" data-icon="*" title="Reset"></button>
	</form>
</dialog>