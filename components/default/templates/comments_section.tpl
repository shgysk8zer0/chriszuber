<!--
	@param string %TITLE%
	@param string %HOME%
	@param string %COMMENTS%
-->

<br />
<button type="button" title="Comment on %TITLE%" data-show-modal="#new_comment" data-icon="c"><b>Post Comment</b></button>
<br /><br />
<dialog id="new_comment">
	<button type="button" title="Close popup" data-close="#new_comment"></button><br />
	<form name="comments" action="%HOME%" method="post" data-confirm="Post this comment? Please remember to be nice.">
		<fieldset id="comments">
			<legend>Comment on <q>%TITLE%</q></legend>
			<label for="comment_author" data-icon="U"></label>
			<input type="text" name="comment_author" id="comment_author" pattern="[\w\.\-, ]+" placeholder="Anonymous" required/><br />
			<label for="comment_email" data-icon="@"></label>
			<input type="email" name="comment_email" id="comment_email" placeholder="user@example.com" required/><br />
			<label for="comment_url" data-icon="K"></label>
			<input type="url" name="comment_url" id="comment_url" placeholder="http://www.example.com"/>
			<hr />
			<textarea name="comment" rows="6" cols="30" placeholder="Enter your comment here. No MarkDown. Only <a>, <code>, and text-formatting tags allowed" required></textarea><br />
			<input type="hidden" name="for_post" value="%URL%" required readonly/>
			<input type="hidden" name="post_title" value="%TITLE%" required readonly/>
			<button type="submit" data-icon="."></button>
		</fieldset>
	</form>
</dialog>
<details id="comments_section" open>
	<summary><button data-icon="i" title="Comments">View Comments</button></summary>
	%COMMENTS%
</details>
