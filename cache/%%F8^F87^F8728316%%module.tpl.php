<?php /* Smarty version 2.6.19, created on 2010-03-16 15:56:02
         compiled from module.tpl */ ?>
<table class="<?php echo $this->_tpl_vars['mod']['css_prefix']; ?>
module" width="100%" cellspacing="0" cellpadding="0">
<tbody>
	<?php if ($this->_tpl_vars['mod']['showtitle'] != 0): ?>
	<tr>
		<td class="<?php echo $this->_tpl_vars['mod']['css_prefix']; ?>
moduletitle">
			<?php echo $this->_tpl_vars['mod']['title']; ?>

			<?php if ($this->_tpl_vars['cfglink']): ?>
				<span class="fast_cfg_link">
					<a href="<?php echo $this->_tpl_vars['cfglink']; ?>
" target="_blank" title="Настроить модуль">
						<img src="/images/icons/configure.gif"/>
					</a>
				</span>
			<?php endif; ?>
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td class="<?php echo $this->_tpl_vars['mod']['css_prefix']; ?>
modulebody">
			<?php echo $this->_tpl_vars['mod']['body']; ?>

		</td>
	</tr>
</tbody>
</table>