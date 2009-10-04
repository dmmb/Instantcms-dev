{* ================================================================================ *}
{* ========================= �������� �����  ====================================== *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>

{if $is_latest}
    <div class="blog_type_menu">

            {if !$ownertype}
                <span class="blog_type_active">����� �������</span>
            {else}
                <a class="blog_type_link" href="/blogs/{$menuid}/latest.html">����� �������</a>
            {/if}

             {if $ownertype == 'all'}
                <span class="blog_type_active">��� �����</span>
             {else}
                <a class="blog_type_link" href="/blogs/{$menuid}/all.html">��� �����</a>
             {/if}

            {if $single_blogs && $multi_blogs}
                {if $ownertype == 'single'}
                    <span class="blog_type_active">������������ <span class="blog_type_num">({$single_blogs})</span></span>
                {else}
                    <a class="blog_type_link" href="/blogs/{$menuid}/single.html">������������ <span class="blog_type_num">({$single_blogs})</span></a>
                {/if}
            {/if}

            {if $single_blogs && $multi_blogs}
                {if $ownertype == 'multi' && $multi_blogs}
                    <span class="blog_type_active">������������ <span class="blog_type_num">({$multi_blogs})</span></span>
                {else}
                    <a class="blog_type_link" href="/blogs/{$menuid}/multi.html">������������ <span class="blog_type_num">({$multi_blogs})</span></a>
                {/if}
            {/if}

    </div>
{/if}

{* ============================== ������ ������� ����� ==================================== *}
{if $is_posts==true}
	<div class="blog_entries">
		{foreach key=tid item=post from=$posts}
			<div class="blog_entry">
				<table width="100%" cellspacing="0" cellpadding="5" class="blog_records">
					<tr>
						<td width="" class="blog_entry_title_td">
							<div class="blog_entry_title">
                                {if $post.blog_url}
                                    <a href="{$post.blog_url}" style="color:gray">{$post.blog_title}</a> &rarr;
                                {/if}
                                <a href="{$post.url}">{$post.title}</a>
                            </div>
							<div class="blog_entry_karma">&uarr; {$post.karma} &darr;</div>
							<div class="blog_entry_info"><a href="/users/{$menuid}/{$post.author_id}/profile.html">{$post.author}</a> &rarr; <span class="blog_entry_date">{$post.fpubdate}</span></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="blog_entry_text">{$post.msg}</div>
							<div class="blog_comments">
								{if ($post.comments > 0)}
									<a class="blog_comments_link" href="{$post.url}#c">{$post.comments|spellcount:'�����������':'�����������':'������������'}</a>
								{else}
									<a class="blog_comments_link" href="{$post.url}#c">��� ������������</a>
								{/if}
							{if $post.tagline != false}
								 <span class="tagline">{$post.tagline}</span>
							{/if}
							{if $myblog || $post.user_id == $uid || $is_admin}
								<span class="editlinks">
									| <a href="/blogs/{$menuid}/{$post.blog_id}/editpost{$post.id}.html" class="blog_entry_edit">�������������</a>
									| <a href="/blogs/{$menuid}/{$post.blog_id}/delpost{$post.id}.html" class="blog_entry_delete">�������</a>				
								</span>
							{/if}
							</div>
						</td>
					</tr>
				</table>
			</div>
		{/foreach}		
	</div>	
	<script type="text/javascript">{$round_corners_js}</script>
	
	{$pagination}
{else}
	<p style="clear:both">��� �������.</p>
{/if}
