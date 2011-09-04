{if $friends_total}
<div id="fr_body">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="action_friends">
  <tr>
    {if $page > 1}
    <td width="20px"><a href="javascript:void(0);" onclick="listFriends({math equation="z - 1" z=$page});" class="arr_btn">&laquo;</a></td>
	{else}
    <td width="85px" id="fr0" {if !$user_id}class="selected"{/if}>
    	<div class="action_fr">
    		<a href="javascript:void(0);" onclick="selectUser(0);" title="{$LANG.ALL_FRIENDS}"><img alt="all" border="0" src="/templates/_default_/images/actions_people.png"  /></a>
        </div>
    </td>
    {/if}
    {foreach key=tid item=friend from=$friends}
        <td width="85px" id="fr{$friend.id}" {if $user_id == $friend.id}class="selected"{/if}>
            <div class="action_fr">
                <a href="javascript:void(0);" onclick="selectUser({$friend.id});" title="{$friend.nickname|escape:'html'}">{$friend.avatar}</a>
            </div>
        </td>
    {/foreach}
    {if $page != $total_pages}
    <td align="right"><a href="javascript:void(0);" onclick="listFriends({math equation="z + 1" z=$page});" class="arr_btn">&raquo;</a></td>
    {else}
    <td>&nbsp;</td>
    {/if}
  </tr>
</table>
{literal}
<script type="text/javascript">
function selectUser(user_id){
	$('input[name=user_id]').val(user_id);
	$('td.selected').removeClass('selected');
	$('#fr'+user_id).addClass('selected');
	$.post('/actions', {user_id: user_id, 'do': 'view_user_feed_only'}, function(data){
		$('#actions_list').html(data);
	});
}
function listFriends(page, user_id){
	var user_id = $('input[name=user_id]').val();
	$.post('/actions', {page: page, user_id: user_id, 'do': 'view_user_friends_only'}, function(data){
		$('#fr_body').html(data);
	});
}
</script>
{/literal}
</div>
{/if}