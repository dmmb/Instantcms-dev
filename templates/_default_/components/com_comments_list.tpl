{* ================================================================================ *}
{* ========================== Список комментариев ================================= *}
{* ================================================================================ *}

{if $comments_count}
	{foreach key=cid item=comment from=$comments}
        {math equation="x+1" x=$cid assign="next"}
		<a name="c{$comment.id}"/>
        <div style="margin-left:{math equation="x*35" x=$comment.level}px;">
        <table class="cmm_entry">
			<tr>
				<td class="cmm_title" valign="middle">
					{if !$comment.is_profile}
						<span class="cmm_author">{$comment.author}</span>
					{else}
						<span class="cmm_author"><a href="{profile_url login=$comment.author.login}">{$comment.author.nickname}</a></span>
					{/if}
                        <a class="cmm_anchor" href="#c{$comment.id}" title="{$LANG.LINK_TO_COMMENT}">#</a>
						<span class="cmm_date">{$comment.fpubdate}</span>
                        {if !$is_user || $comment.is_voted}
                            <span class="cmm_votes">{$comment.votes}</span>
                        {else}
                            <span class="cmm_votes" id="votes{$comment.id}">
                                <table border="0" cellpadding="0" cellspacing="0"><tr>
                                <td>{$comment.votes}</td>
                                <td><a href="javascript:void(0);" onclick="voteComment({$comment.id}, -1);" title="{$LANG.BAD_COMMENT}"><img border="0" alt="-" src="/components/comments/images/vote_down.gif" style="margin-left:8px"/></a></td>
                                <td><a href="javascript:void(0);" onclick="voteComment({$comment.id}, 1);" title="{$LANG.GOOD_COMMENT}"><img border="0" alt="+" src="/components/comments/images/vote_up.gif" style="margin-left:2px"/></a></td>
                                </tr></table>
                            </span>
                        {/if}
				</td>
			</tr>
			<tr>
				{if $comment.is_profile}
					<td valign="top">
						<table width="100%" cellpadding="1" cellspacing="0">
							<tr>
								<td width="70" height="70"  align="center" valign="top" class="cmm_avatar">
									<a href="{profile_url login=$comment.author.login}">{$comment.user_image}</a>
								</td>
								<td class="cmm_content_av" valign="top"><div>
				{else}
					<td class="cmm_content" valign="top">
				{/if}
					{if $comment.show}
						{$comment.content}
					{else}
						<a href="javascript:void(0)" onclick="expandComment({$comment.id})" id="expandlink{$comment.id}">{$LANG.SHOW_COMMENT}</a>
						<div id="expandblock{$comment.id}" style="display:none">{$comment.content}</div>
					{/if}
                            {if $is_user}
                                <div style="display:block; margin-top:20px;">
                                    [<a href="javascript:void(0)" onclick="addComment('{php}echo md5(session_id());{/php}', '{$target}', '{$target_id}', {$comment.id})">{$LANG.REPLY}</a>]
                                    {if $is_admin}
                                        [<a href="/admin/index.php?view=components&do=config&id=7&opt=edit&item_id={$comment.id}">{$LANG.EDIT}</a>]
                                    {/if}
                                    {if $is_admin || ($comment.is_my && $user_can_delete) || $user_can_moderate}
                                        [<a href="/comments/delete/{$comment.id}">{if $comments[$next].level > $comment.level}{$LANG.DELETE_BRANCH}{else}{$LANG.DELETE}{/if}</a>]
                                    {/if}
                                </div>
                            {/if}
						
						{if $comment.is_profile}
							</div></td></tr></table>
						{/if}
					</td>
				</tr>
			</table>
            <div id="cm_addentry{$comment.id}" class="reply" style="display:none"></div>
        </div>
	{/foreach}

{else}
    {* ================================= Нет комментариев =============================== *}
	<p>{$LANG.NOT_COMMENT_TEXT}</p>
{/if}