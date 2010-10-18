<div class="{$mod.css_prefix}module">
    {if $mod.showtitle neq 0}
        <div class="{$mod.css_prefix}moduletitle">
            {$mod.title}
            {if $cfglink}
                <span class="fast_cfg_link">
                    <a href="{$cfglink}" target="_blank" title="Настроить модуль">
                        <img src="/images/icons/configure.gif"/>
                    </a>
                </span>
            {/if}
        </div>
    {/if}
    <div class="{$mod.css_prefix}modulebody">{$mod.body}</div>

</div>
