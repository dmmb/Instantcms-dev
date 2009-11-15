{if $latest}    
    <div style="margin-bottom:8px"><strong>Новые файлы</strong></div>

    <table width="100%" cellpadding="0" cellspacing="2" border="0" style="margin-bottom:10px">
        {foreach key=id item=file from=$latest}
            <tr>
                <td><a href="/users/files/download{$file.id}.html">{$file.filename}</a> - [{$file.description}] - {$file.size} Мб</td>
                <td width="35">
                    <a href="{profile_url login=$file.user_login}" title="{$file.user_nickname}">
                        <img src="/images/icons/users.gif" border="0" />
                    </a> 
                    <a href="/users/0/{$file.user_id}/files.html" title="Все файлы пользователя">
                        <img src="/images/markers/folder.png" border="0" />
                    </a>
                </td>
            </tr>
        {/foreach}
    </table>
{/if}
{if $popular}
    <div style="margin-bottom:8px"><strong>Популярные файлы</strong></div>

    <table width="100%" cellpadding="0" cellspacing="2" border="0" style="margin-bottom:10px">
        {foreach key=id item=file from=$popular}
            <tr>
                <td><a href="/users/files/download{$file.id}.html">{$file.filename}</a>  - [{$file.description}] - {$file.size} Мб</td>
                <td width="35">
                    <a href="{profile_url login=$file.user_login}" title="{$file.user_nickname}">
                        <img src="/images/icons/users.gif" border="0" />
                    </a> 
                    <a href="/users/0/{$file.user_id}/files.html" title="Все файлы пользователя">
                        <img src="/images/markers/folder.png" border="0" />
                    </a>
                </td>
            </tr>
        {/foreach}
    </table>
{/if}
{if $list}    
    <div style="margin-bottom:8px"><strong>Список файлов пользователей</strong></div>
    <table width="100%" cellpadding="0" cellspacing="2" border="0" style="margin-bottom:10px">
        {foreach key=id item=file from=$list}
            <tr>
                <td><a href="/users/files/download{$file.id}.html">{$file.filename}</a> - [{$file.description}] - {$file.size} Мб</td>
                <td width="35">
                    <a href="{profile_url login=$file.user_login}" title="{$file.user_nickname}">
                        <img src="/images/icons/users.gif" border="0" />
                    </a> 
                    <a href="/users/0/{$file.user_id}/files.html" title="Все файлы пользователя">
                        <img src="/images/markers/folder.png" border="0" />
                    </a>
                </td>
            </tr>
        {/foreach}
    </table>
{/if}
{if $cfg.sw_stats}
    <div><strong>Всего файлов:</strong> {$stats.total_files}</div>
    <div><strong>Общий размер:</strong> {$stats.total_size} Мб</div>
{/if}