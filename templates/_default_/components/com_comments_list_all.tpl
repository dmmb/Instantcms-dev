{* ================================================================================ *}
{* ========================== Список комментариев ================================= *}
{* ================================================================================ *}
<h1 class="con_heading">{$LANG.COMMENTS_ON_SITE}</h1>
{if $comments_count}
	{foreach key=cid item=comment from=$comments}
    	<h3 class="cmm_all_title"><span class="cmm_all_author">{if !$comment.is_profile}{$comment.author}{else}<a href="{profile_url login=$comment.author.login}">{$comment.author.nickname}</a>{/if} {if $is_admin}{$comment.ip}{/if}</span> <span class="cmm_all_gender"> {$comment.gender}</span>  &rarr; <a class="cmm_all_target" href="{$comment.target_link}#c{$comment.id}" title="{$LANG.LINK_TO_COMMENT}">{$comment.target_title}</a> <span class="cmm_date">{$comment.fpubdate}</span></h3>
        <table class="cmm_entry">
			<tr>
				{if $comment.is_profile}
					<td valign="top">
						<table width="100%" cellpadding="1" cellspacing="0">
							<tr>
								<td width="70" height="70"  align="center" valign="top" class="cmm_avatar">
									<a href="{profile_url login=$comment.author.login}">{$comment.user_image}</a>
								</td>
								<td class="cmm_content_av" valign="top">
				{else}
					<td class="cmm_all_content" valign="top">
				{/if}
					{if $comment.show}
						{$comment.content}
					{else}
						<a href="javascript:void(0)" onclick="expandComment({$comment.id})" id="expandlink{$comment.id}">{$LANG.SHOW_COMMENT}</a>
						<div id="expandblock{$comment.id}" style="display:none">{$comment.content}</div>
					{/if}
						{if $comment.is_profile}
							</td></tr></table>
						{/if}
					</td>
                    <td align="right" valign="middle"><span class="cmm_all_votes" style="font-size:18px;">{$comment.votes}</span></td>
				</tr>
			</table>
	{/foreach}
{$pagebar}
{else}
    {* ================================= Нет комментариев =============================== *}
	<p>{$LANG.NOT_COMMENT_TEXT}</p>
{/if}