<?php /* Smarty version 2.6.19, created on 2010-03-16 15:56:01
         compiled from mod_menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'mod_menu.tpl', 14, false),)), $this); ?>
<?php if ($this->_tpl_vars['cfg']['jtree']): ?>
    <div><ul id="<?php echo $this->_tpl_vars['menu']; ?>
" class="filetree treeview-famfamfam">
<?php else: ?>
    <div><ul id="<?php echo $this->_tpl_vars['menu']; ?>
" class="<?php echo $this->_tpl_vars['menu']; ?>
">
<?php endif; ?>

<?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
    <?php if ($this->_tpl_vars['item']['allow_group'] == -1 || $this->_tpl_vars['item']['allow_group'] == $this->_tpl_vars['user_group'] || $this->_tpl_vars['is_admin']): ?>
        <?php if ($this->_tpl_vars['item']['published']): ?>
            <?php if ($this->_tpl_vars['item']['parent_id'] != $this->_tpl_vars['hide_parent']): ?>

                <?php if ($this->_tpl_vars['item']['NSLevel'] > 1): ?> <?php $padding = '0px 0px 0px 15px'; ?> <?php else: ?> <?php $padding = '0px'; ?> <?php endif; ?>
                <?php if ($this->_tpl_vars['item']['NSLevel'] < $this->_tpl_vars['last_level']): ?>                    
                    <?php echo smarty_function_math(array('equation' => "x - y",'x' => $this->_tpl_vars['last_level'],'y' => $this->_tpl_vars['item']['NSLevel'],'assign' => 'tail'), $this);?>

                    <?php unset($this->_sections['foo']);
$this->_sections['foo']['name'] = 'foo';
$this->_sections['foo']['start'] = (int)0;
$this->_sections['foo']['loop'] = is_array($_loop=$this->_tpl_vars['tail']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['foo']['step'] = ((int)1) == 0 ? 1 : (int)1;
$this->_sections['foo']['show'] = true;
$this->_sections['foo']['max'] = $this->_sections['foo']['loop'];
if ($this->_sections['foo']['start'] < 0)
    $this->_sections['foo']['start'] = max($this->_sections['foo']['step'] > 0 ? 0 : -1, $this->_sections['foo']['loop'] + $this->_sections['foo']['start']);
else
    $this->_sections['foo']['start'] = min($this->_sections['foo']['start'], $this->_sections['foo']['step'] > 0 ? $this->_sections['foo']['loop'] : $this->_sections['foo']['loop']-1);
if ($this->_sections['foo']['show']) {
    $this->_sections['foo']['total'] = min(ceil(($this->_sections['foo']['step'] > 0 ? $this->_sections['foo']['loop'] - $this->_sections['foo']['start'] : $this->_sections['foo']['start']+1)/abs($this->_sections['foo']['step'])), $this->_sections['foo']['max']);
    if ($this->_sections['foo']['total'] == 0)
        $this->_sections['foo']['show'] = false;
} else
    $this->_sections['foo']['total'] = 0;
if ($this->_sections['foo']['show']):

            for ($this->_sections['foo']['index'] = $this->_sections['foo']['start'], $this->_sections['foo']['iteration'] = 1;
                 $this->_sections['foo']['iteration'] <= $this->_sections['foo']['total'];
                 $this->_sections['foo']['index'] += $this->_sections['foo']['step'], $this->_sections['foo']['iteration']++):
$this->_sections['foo']['rownum'] = $this->_sections['foo']['iteration'];
$this->_sections['foo']['index_prev'] = $this->_sections['foo']['index'] - $this->_sections['foo']['step'];
$this->_sections['foo']['index_next'] = $this->_sections['foo']['index'] + $this->_sections['foo']['step'];
$this->_sections['foo']['first']      = ($this->_sections['foo']['iteration'] == 1);
$this->_sections['foo']['last']       = ($this->_sections['foo']['iteration'] == $this->_sections['foo']['total']);
?>
                        </ul></li>
                    <?php endfor; endif; ?>
                <?php endif; ?>
                <?php if ($this->_tpl_vars['item']['NSRight'] - $this->_tpl_vars['item']['NSLeft'] == 1): ?>
                    <li style="padding:<?php echo $padding; ?>">
                        <span class="file" style="background: url(<?php echo $this->_tpl_vars['item']['fileicon']; ?>
) 0 0 no-repeat;"><?php echo $this->_tpl_vars['item']['link']; ?>
</span>
                    </li>
                <?php else: ?>
                    <li <?php if ($this->_tpl_vars['currentmenu']['NSLeft'] > $this->_tpl_vars['item']['NSLeft'] && $this->_tpl_vars['currentmenu']['NSRight'] < $this->_tpl_vars['item']['NSRight']): ?>class="open"<?php endif; ?> style="padding:<?php echo $padding; ?>">
                        <span class="folder" style="background: url(<?php echo $this->_tpl_vars['item']['foldericon']; ?>
) 0 0 no-repeat;">
                           <?php if ($this->_tpl_vars['cfg']['jtree']): ?>                           
                                <a href="javascript:"><?php echo $this->_tpl_vars['item']['title']; ?>
</a>
                           <?php else: ?>
                                <?php echo $this->_tpl_vars['item']['link']; ?>

                           <?php endif; ?>
                        </span>
                        <ul>
                <?php endif; ?>
                <?php $this->assign('last_level', $this->_tpl_vars['item']['NSLevel']); ?>
            <?php endif; ?>
        <?php else: ?>
            <?php $hide_parent = $item['id']; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</ul></div>

<?php if ($this->_tpl_vars['cfg']['jtree']): ?>
    <script type="text/javascript">
        <?php echo '
            $(document).ready(function(){
                 $(".filetree").treeview({
                    animated: true,
                    collapsed: true,
                    unique: false
                    });
                }
            );
        '; ?>

     </script>
<?php endif; ?>