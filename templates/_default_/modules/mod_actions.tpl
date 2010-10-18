{if $actions}
    <div class="actions_list">
        {foreach key=aid item=action from=$actions}
            <div class="action_entry act_{$action.name}">
                <div class="action_date">{$action.pubdate}</div>
                <div class="action_title">
                    <a href="{$action.user_url}" class="action_user">{$action.user_nickname}</a>
                    {$action.message}{if $action.description}:{/if}
                </div>
                {if $action.description}
                    <div class="action_details">{$action.description}</div>
                {/if}
            </div>
        {/foreach}
    </div>
{/if}