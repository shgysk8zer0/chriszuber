<!--
	Template for posts/articles

	@param string %TITLE%
	@param string %TAGS%
	@param string %CONTENT%
	@param string %AUTHOR%
	@param string %AUTHOR_URL%
	@param string %DATE%
	@param string %DATETIME%
	@param string %HOME%
-->
		<article>
			<header>
				<h1 itemprop="headline">%TITLE%</h1>
				<nav>
					<details>
						<summary title="Tags" data-icon=","></summary>
						<div itemprop="keywords">%TAGS%</div>
					</details>
					<!--<a href="%URL%/#comments_section" title="Scroll to Comments">Comments</a>-->
				</nav>
			</header>
			<section itemprop="text">%CONTENT%</section>
			<footer>
				<details>
					<summary>
						<img alt="Creative Commons License" src="images/logos/CreativeCommons.svgz" />
					</summary>
						<div>
							<span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">%TITLE%</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="%AUTHOR_URL%?rel=author" property="cc:attributionName" rel="cc:attributionURL author" itemprop="author">%AUTHOR%</a> is licensed under a <a rel="license" itemprop="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.<time datetime="%DATETIME%" itemprop="datePublished">%DATE%</time>
						</div>
				</details>
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
<!--
	Navigation Section: How will I get previous and next?
	Try something like :
	$DB->prepare("
		SELECT `url` AS `next`
		FROM `posts`
		WHERE `id` = (
			SELECT `id`
			FROM `posts`
			WHERE `title` = :title
		) + 1
	")
				<hr />
				<a href="%PREV%" rel="prev" title="Previous"></a>
				<a href="%HOME%" rel="bookmark" title="home" class="logo"><img src="images/icons/home.svgz"/ alt="Home"></a>
				<a href="%NEXT%" rel="next" title="Next"></a>
-->
				<details id="comments_section" open>
					<summary><button data-icon="i" title="Comments">View Comments</button></summary>
					%COMMENTS%
				</details>
			</footer>
		</article>
