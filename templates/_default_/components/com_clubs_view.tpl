{* ================================================================================ *}
{* ========================= Просмотр списка клубов =============================== *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>

{if $can_create}
	<div class="new_club">
		Вы можете <a href="/clubs/{$menuid}/create.html">создать новый клуб</a>
	</div>
{/if}

{if $total>0}

	{foreach key=tid item=club from=$clubs}
		<div class="club_entry">
			<div class="image">
				<a href="/clubs/{$menuid}/{$club.id}" title="{$club.title}">
					<img src="/images/clubs/small/{$club.imageurl}" border="0" alt="{$club.title}"/>
				</a>
			</div>					
			<div class="data">
				<div class="title">
					<a href="/clubs/{$menuid}/{$club.id}">{$club.title}</a>
				</div>
				<div class="details">
					<span class="rating"><strong>Рейтинг</strong> &mdash; {$club.rating}</span>
					<span class="members"><strong>{$club.members|spellcount:'участник':'участника':'участников'}</strong></span>
				</div>
			</div>
		</div>
	{/foreach}
	
	{if $pagination}<div style="margin-top:40px">{$pagination}</div>{/if}
{else}
	<p style="clear:both">Нет активных клубов.</p>
{/if}
