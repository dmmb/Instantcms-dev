{if $threads}
    {php}$row=1;{/php}
    <table class="threads_table" width="100%" cellspacing="0" cellpadding="5" border="0">
        <tr>
          <td colspan="2" class="darkBlue-LightBlue">{$LANG.THREADS_ACT}</td>
          <td class="darkBlue-LightBlue">{$LANG.AUTHOR}</td>
          <td class="darkBlue-LightBlue">{$LANG.FORUM_ACT}</td>
          <td class="darkBlue-LightBlue">{$LANG.LAST_POST}</td>
        </tr>
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
                <td width="30" class="{php}echo $class{/php}" align="center" valign="middle"><img alt="{$LANG.ATTACHED_THREAD}" src="/components/forum/images/pinned.png" border="0" title="{$LANG.ATTACHED_THREAD}" /></td>
            {else}
                {if $thread.closed}
                    <td width="30" class="{php}echo $class{/php}" align="center" valign="middle"><img alt="{$LANG.THREAD_CLOSE}" src="/components/forum/images/closed.png" border="0" title="{$LANG.THREAD_CLOSE}" /></td>
                {else}
                    {if $thread.is_new}
                        <td width="30" class="{php}echo $class{/php}" align="center" valign="middle"><img alt="{$LANG.HAVE_NEW_MESS}" src="/components/forum/images/new.png" border="0" title="{$LANG.HAVE_NEW_MESS}" /></td>
                    {else}
                        <td width="30" class="{php}echo $class{/php}" align="center" valign="middle"><img alt="{$LANG.NOT_NEW_MESS}" src="/components/forum/images/old.png" border="0" title="{$LANG.NOT_NEW_MESS}" /></td>
                    {/if}
                {/if}
            {/if}
            <td width="" class="{php}echo $class{/php}" align="left">
                <div class="thread_link"><a href="/forum/thread{$thread.id}.html">{$thread.title}</a>
                    {if $thread.pages>1}
                        <span class="thread_pagination" title="{$LANG.PAGES}"> (
                            {php}
                                for ($tp=1; $tp<=$this->_tpl_vars['thread']['pages']; $tp++){
                                	if($tp>5 && ($this->_tpl_vars['thread']['pages'] > 6)){
                                    	echo '...<a href="/forum/thread'.$this->_tpl_vars['thread']['id'].'-'.$this->_tpl_vars['thread']['pages'].'.html" title="'.$this->_tpl_vars['LANG']['LAST'].'">'.$this->_tpl_vars['thread']['pages'].'</a>'; break;
                                    } else {
                                    	echo '<a href="/forum/thread'.$this->_tpl_vars['thread']['id'].'-'.$tp.'.html" title="'.$this->_tpl_vars['LANG']['PAGE'].' '.$tp.'">'.$tp.'</a>';
                                        if($tp<$this->_tpl_vars['thread']['pages']) { echo ', '; }
                                    }
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
            <td width="120" style="font-size:12px; color:#375E93" class="{php}echo $class{/php}">
                <strong>{$LANG.HITS}:</strong> {$thread.hits}<br/>
                <strong>{$LANG.REPLIES}:</strong> {$thread.answers}
            </td>
            <td width="200" style="font-size:12px" class="{php}echo $class{/php}">{$thread.last_message}</td>
        </tr>
        {php}$row++;{/php}
    {/foreach}
    </table>
{/if}