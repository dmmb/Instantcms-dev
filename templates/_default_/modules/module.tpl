<div class="{$mod.css_prefix}module">
    {if $mod.showtitle neq 0}
        <div class="{$mod.css_prefix}moduletitle">
            {$mod.title}
            {if $cfglink}
                <span class="fast_cfg_link">
                    <a href="javascript:moduleConfig({$mod.module_id});" title="��������� ������">
                        <img src="/templates/_default_/images/icons/settings.png"/>
                    </a>
                </span>
            {/if}
        </div>
    {/if}
    <div class="{$mod.css_prefix}modulebody">{$mod.body}</div>

</div>
