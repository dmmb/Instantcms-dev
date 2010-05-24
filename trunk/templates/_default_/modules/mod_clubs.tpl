{* ================================================================================ *}
{* ========================= Просмотр списка клубов =============================== *}
{* ================================================================================ *}
<div class="mod_clubs">
{foreach key=tid item=club from=$clubs}
	<div class="club_entry">
		<div class="image">
			<a href="/clubs/{$club.id}" title="{$club.title}">
				<img src="/images/clubs/small/{$club.imageurl}" border="0" alt="{$club.title}"/>
			</a>
		</div>					
		<div class="data">
			<div class="title">
				<a href="/clubs/{$club.id}">{$club.title}</a>
			</div>
			<div class="details">
				<span class="rating"><strong>Рейтинг</strong> &mdash; {$club.rating}</span>
				<span class="members"><strong>{$club.members|spellcount:'участник':'участника':'участников'}</strong></span>
			</div>
		</div>
	</div>
{/foreach}
</div>