{* ================================================================================ *}
{* ========================= Просмотр списка клубов =============================== *}
{* ================================================================================ *}

{if $can_create}
	<div class="new_club">
		{$LANG.YOU_CAN} <a href="/clubs/create.html">{$LANG.TO_CREATE_NEW_CLUB}</a>
	</div>
{/if}

<div class="con_heading">{$pagetitle}</div>

{if $total>0}

	{foreach key=tid item=club from=$clubs}
		<div class="club_entry{if $club.is_vip}_vip{/if}">
			<div class="image">
				<a href="/clubs/{$club.id}" title="{$club.title|escape:'html'}" class="{$club.clubtype}">
					<img src="/images/clubs/small/{$club.imageurl}" border="0" alt="{$club.title|escape:'html'}"/>
				</a>
			</div>					
			<div class="data">
				<div class="title">
					<a href="/clubs/{$club.id}" class="{$club.clubtype}" {if $club.clubtype=='private'}title="Приватный клуб"{/if}>{$club.title}</a>
				</div>
				<div class="details">
                    {if $club.is_vip}
                        <span class="vip"><strong>{$LANG.VIP_CLUB}</strong></span>
                    {else}
    					<span class="rating"><strong>{$LANG.RATING}</strong> &mdash; {$club.rating}</span>
                    {/if}
					<span class="members"><strong>{$club.members|spellcount:$LANG.USER:$LANG.USER2:$LANG.USER10}</strong></span>
				</div>
			</div>
		</div>
	{/foreach}
	
	{if $pagination}<div style="margin-top:40px">{$pagination}</div>{/if}
{else}
	<p style="clear:both">{$LANG.NOT_ACTIVE_CLUBS}</p>
{/if}
