<?php /* Smarty version 2.6.19, created on 2010-03-16 15:56:02
         compiled from mod_forum_web2.tpl */ ?>
<table width="100%" cellspacing="0" cellpadding="5" border="0" >
    <?php $_from = $this->_tpl_vars['threads']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['thread']):
?>

        <tr>
            <td align="left" class="mod_fweb2_date" width="70"><div style="text-align:center"><?php echo $this->_tpl_vars['thread']['date']; ?>
</div></td>
            <td width="13">
                <?php if (! $this->_tpl_vars['thread']['secret']): ?>
                    <img src="/modules/mod_forum/user.gif" border="0" />
                <?php else: ?>
                    <img src="/modules/mod_forum/hidden.gif" border="0" title="Скрытая тема - видна только вашей группе"/>
                <?php endif; ?>
            </td>
            <td style="padding-left:0px"><a href="<?php echo $this->_tpl_vars['thread']['authorhref']; ?>
" class="mod_fweb2_userlink"><?php echo $this->_tpl_vars['thread']['author']; ?>
</a> <?php echo $this->_tpl_vars['thread']['act']; ?>
 &laquo;<a href="<?php echo $this->_tpl_vars['thread']['topichref']; ?>
" class="mod_fweb2_topiclink"><?php echo $this->_tpl_vars['thread']['topic']; ?>
</a>&raquo;
            <?php if ($this->_tpl_vars['cfg']['showforum'] != 0): ?> на форуме &laquo;<a href="<?php echo $this->_tpl_vars['thread']['forumhref']; ?>
"><?php echo $this->_tpl_vars['thread']['forum']; ?>
</a>&raquo;<?php endif; ?></td>
        </tr>

        <?php if ($this->_tpl_vars['cfg']['showtext'] != 0): ?>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2"><div class="mod_fweb2_shorttext"><?php echo $this->_tpl_vars['thread']['msg']; ?>
</div></td>
        </tr>
        <?php endif; ?>

    <?php endforeach; endif; unset($_from); ?>
</table>