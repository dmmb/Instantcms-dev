{* ================================================================================ *}
{* ========================= Просмотр поста в блоге =============================== *}
{* ================================================================================ *}

<h1 class="con_heading">{$post.title}</h1>

<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<div class="blog_post_data">
				<div style="margin-bottom:10px"><strong>Автор:</strong> <a href="/users/{$menuid}/{$post.author_id}/profile.html">{$post.author}</a></div>
				<div><strong>Опубликовано:</strong> {$post.fpubdate}</div>
				<div><strong>Блог:</strong> <a href="/blogs/{$menuid}/{$blog.seolink}">{$blog.title}</a></div>
				{if $blog.showcats}
					<div><strong>Рубрика:</strong> <a href="/blogs/{$menuid}/{$blog.seolink}{if $post.cat_id}/cat-{$post.cat_id}{/if}">{$cat}</a></div>
				{/if}
				{if $post.edit_times}
					<div><strong>Редактировалось:</strong> {$post.edit_times|spellcount:'раз':'раза':'раз'} &mdash; {if $post.edit_times>1}последний{/if} {$post.feditdate}</div>
				{/if}
				{if $post.feel}
					<div><strong>Настроение:</strong> {$post.feel}</div>
				{/if}
				{if $post.music}
					<div><strong>Играет:</strong> {$post.music}</div>
				{/if}
			</div>
		</td>
		<td width="100" valign="top">
			{$karma_form}
		</td>
	</tr>
</table>

<div class="blog_post_body">{$msg}</div>

{if $nav != false}
	<div class="blog_post_nav">{$nav}</div>
{/if}

{$tag_bar}