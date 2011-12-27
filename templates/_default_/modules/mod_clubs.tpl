{* ================================================================================ *}
{* ========================= Просмотр списка клубов =============================== *}
{* ================================================================================ *}
{if $is_clubs}
<div class="mod_clubs">
{foreach key=tid item=club from=$clubs}
	<div class="club_entry">
		<div class="image">
			<a href="/clubs/{$club.id}" title="{$club.title|escape:'html'}">
				<img src="/images/clubs/small/{$club.imageurl}" border="0" alt="{$club.title|escape:'html'}"/>
			</a>
		</div>					
		<div class="data">
			<div class="title">
				<a href="/clubs/{$club.id}">{$club.title}</a>
			</div>
			<div class="details">
				<span class="rating"><strong>Рейтинг</strong> &mdash; {$club.rating}</span>
				<span class="members"><strong>{$club.members|spellcount:$LANG.CLUBS_USER:$LANG.CLUBS_USER2:$LANG.CLUBS_USER10}</strong></span>
			</div>
		</div>
	</div>
{/foreach}
</div>
{else}
    <p>{$LANG.LATESTCLUBS_NOT_CLUBS}</p>
{/if}
