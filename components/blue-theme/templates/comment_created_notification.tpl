<!--
	Email template for notifying SERVER_ADMIN of new comments on a post
	@param %AUTHOR_URL%
	@param %AUTHOR%
	@param %POST_URL%
	@param %POST%
	@param %TIME%
	@param %COMMENT%
-->
<div>
	<h1>
		New comment from <a href="%AUTHOR_URL%" target="_blank">%AUTHOR%</a> on <a href="%POST_URL%" target="_blank">%POST%</a> at <time>%TIME%</time><br />
	</h1>
	%COMMENT%<br />
	<a href="mailto:%AUTHOR_EMAIL%">Reply to %AUTHOR%</a>
</div>
