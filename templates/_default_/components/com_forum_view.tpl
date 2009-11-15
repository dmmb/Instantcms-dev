{* ================================================================================ *}
{* =========================== Список форумов и категорий ========================= *}
{* ================================================================================ *}

{* ------ Список подфорумов -------- *}

{if $subforums_count}
    <table width="100%" cellspacing="0" cellpadding="5"  class="forum_toolbar">
        <tr>
            <td width="16"><img src="/components/forum/images/toolbar/subforums.gif"/></td>
            <td><div class="subforumshead">Подфорумы</div></td>
        </tr>
    </table>

    <table class="forums_table" width="100%" cellspacing="0" cellpadding="8" border="0" bordercolor="#999999" >
        {php}$row=1;{/php}
        {foreach key=id item=subf from=$subforums}
            {php}
                if ($row % 2) {
                    $class='row1';
                } else {
                    $class='row2';
                }
            {/php}
            <tr>
                <td width="40" class="{php}echo $class{/php}" align="center" valign="top">
                    <img src="/components/forum/images/forum.gif" border="0" />
                </td>
                <td width="" class="{php}echo $class{/php}" align="left" valign="top">
                    <div class="forum_link"><a href="/forum/{$menuid}/{$subf.id}">{$subf.title}</a></div>
                    <div class="forum_desc">{$subf.description}</div>
                    {if $subf.subforums}
                        <div class="forum_subs"><span class="forum_subs_title">Подфорумы:</span> {$subf.subforums}</div>
                    {/if}
                </td>
                <td width="120" class="{php}echo $class{/php}" style="font-size:10px" valign="top">{$subf.messages}</td>
                <td width="250" style="font-size:10px" class="{php}echo $class{/php}" valign="top">{$subf.lastmessage}</td>
            </tr>
            {php}$row++;{/php}
        {/foreach}
    </table>
{/if}


{* ------ Тулбар с кнопками -------- *}

<table width="100%" cellspacing="0" cellpadding="5"  class="forum_toolbar">
    <tr>
        {if $user_id}
            <td class="forum_toollinks">
                <table cellspacing="2" cellpadding="2">
                    <tr>
                        <td width="16"><img src="/components/forum/images/toolbar/newthread.gif"/></td>
                        <td><a href="/forum/{$menuid}/{$forum.id}/newthread.html"><strong>Новая тема</strong></a></td>
                    </tr>
                </table>
            </td>
        {/if}
        {if $threads_count}
            <td width="5">&nbsp;</td>
            {$threads_page_select}
        {/if}
    </tr>
</table>

{* ------ Cписок тем -------- *}

{if $threads_count}
    {php}$row=1;{/php}
    <table class="threads_table" width="100%" cellspacing="0" cellpadding="5" border="0">
    {foreach key=id item=thread from=$threads}
        {php}
            if ($row % 2) {
                $class='row1';
            } else {
                $class='row2';
            }
        {/php}
        <tr>
            {if $thread.pinned}
                <td width="30" class="{php}echo $class{/php}" align="center" valign="middle"><img alt="Прикрепленная тема" src="/components/forum/images/pinned.gif" border="0" /></td>
            {else}
                {if $thread.closed}
                    <td width="30" class="{php}echo $class{/php}" align="center" valign="middle"><img alt="Тема закрыта" src="/components/forum/images/closed.gif" border="0" /></td>
                {else}
                    {if $thread.is_new}
                        <td width="30" class="{php}echo $class{/php}" align="center" valign="middle"><img alt="Есть новые сообщения" src="/components/forum/images/new.gif" border="0" /></td>
                    {else}
                        <td width="30" class="{php}echo $class{/php}" align="center" valign="middle"><img alt="Нет новых сообщений" src="/components/forum/images/old.gif" border="0" /></td>
                    {/if}
                {/if}
            {/if}
            <td width="" class="{php}echo $class{/php}" align="left">
                <div class="thread_link"><a href="/forum/{$menuid}/thread{$thread.id}.html">{$thread.title}</a>
                    {if $pages>1}
                        <span class="thread_pagination"> (
                            {php}
                                for ($tp=1; $tp<=$pages; $tp++){
                                    echo '<a href="/forum/'.$menuid.'/thread'.$t['id'].'-'.$tp.'.html" title="Страница '.$tp.'">'.$tp.'</a>';
                                }
                            {/php}
                        ) </span>
                    {/if}
                </div>
                {if $thread.description}
                    <div class="thread_desc">{$thread.description}</div>
                {/if}
            </td>
            <td width="120" style="font-size:12px" class="{php}echo $class{/php}"><a href="{profile_url login=$thread.author.login}">{$thread.author.nickname}</a></td>
            <td width="120" style="font-size:12px" class="{php}echo $class{/php}">
                <strong>Просмотров:</strong> {$thread.hits}<br/>
                <strong>Ответов:</strong> {$thread.answers}
            </td>
            <td width="200" style="font-size:12px" class="{php}echo $class{/php}">{$thread.last_message}</td>
        </tr>
        {php}$row++;{/php}
    {/foreach}
    </table>
    {$pagination}
{else}
    <p>Нет тем в этом форуме.</p>
{/if}