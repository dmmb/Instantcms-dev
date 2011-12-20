<table width="100%" cellspacing="0" cellpadding="5" border="0" >
    {foreach key=tid item=thread from=$threads}

        <tr>
            <td align="left" class="mod_fweb2_date" width="70"><div style="text-align:center">{$thread.date}</div></td>
            <td width="13">
                {if !$thread.secret}
                    <img src="/templates/_default_/images/icons/user_comment.png" border="0" />
                {else}
                    <img src="/templates/_default_/images/icons/user_silhouette.png" border="0" title="Скрытая тема - видна только вашей группе"/>
                {/if}
            </td>
            <td style="padding-left:0px"><a href="{$thread.authorhref}" class="mod_fweb2_userlink">{$thread.author}</a> {$thread.act} &laquo;<a href="{$thread.topichref}" class="mod_fweb2_topiclink">{$thread.topic}</a>&raquo;
            {if $cfg.showforum neq 0} на форуме &laquo;<a href="{$thread.forumhref}">{$thread.forum}</a>&raquo;{/if}</td>
        </tr>

        {if $cfg.showtext neq 0}
        <tr>
            <td>&nbsp;</td>
            <td colspan="2"><div class="mod_fweb2_shorttext">{$thread.msg|truncate:70}</div></td>
        </tr>
        {/if}

    {/foreach}
</table>