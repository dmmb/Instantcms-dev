<table class="{$mod.css_prefix}module" width="100%" cellspacing="0" cellpadding="0">
<tbody>
	{if $mod.showtitle neq 0}
	<tr>
		<td class="{$mod.css_prefix}moduletitle">
			{$mod.title}
			{if $cfglink}
				<span class="fast_cfg_link">
					<a href="{$cfglink}" target="_blank" title="Настроить модуль">
						<img src="/images/icons/configure.gif"/>
					</a>
				</span>
			{/if}
		</td>
	</tr>
	{/if}
	<tr>
		<td class="{$mod.css_prefix}modulebody">
			{$mod.body}
		</td>
	</tr>
</tbody>
</table>