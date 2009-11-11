{* ================================================================================ *}
{* ========================= Просмотр списка клубов =============================== *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>

{if $can_create}
	<div class="new_club">
		{$LANG.YOU_CAN} <a href="/clubs/{$menuid}/create.html">{$LANG.TO_CREATE_NEW_CLUB}</a>
	</div>
{/if}

{if $total>0}

	{foreach key=tid item=club from=$clubs}
		<div class="club_entry">
			<div class="image">
				<a href="/clubs/{$menuid}/{$club.id}" title="{$club.title}" class="{$club.clubtype}">
					<img src="/images/clubs/small/{$club.imageurl}" border="0" alt="{$club.title}"/>
				</a>
			</div>					
			<div class="data">
				<div class="title">
					<a href="/clubs/{$menuid}/{$club.id}" class="{$club.clubtype}" {if $club.clubtype=='private'}title="Приватный клуб"{/if}>{$club.title}</a>
				</div>
				<div class="details">
					<span class="rating"><strong>{$LANG.RATING}</strong> &mdash; {$club.rating}</span>
					<span class="members"><strong>{$club.members|spellcount:$LANG.USER:$LANG.USER2:$LANG.USER10}</strong></span>
				</div>
			</div>
		</div>
	{/foreach}
	
	{if $pagination}<div style="margin-top:40px">{$pagination}</div>{/if}
{else}
	<p style="clear:both">{$LANG.NOT_ACTIVE_CLUBS}</p>
{/if}
