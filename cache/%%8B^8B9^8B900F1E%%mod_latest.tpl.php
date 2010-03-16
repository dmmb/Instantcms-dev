<?php /* Smarty version 2.6.19, created on 2010-03-16 15:56:02
         compiled from mod_latest.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'spellcount', 'mod_latest.tpl', 20, false),)), $this); ?>
<?php $_from = $this->_tpl_vars['articles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['aid'] => $this->_tpl_vars['article']):
?>
	<div class="mod_latest_entry">	
	    <a class="mod_latest_title" href="<?php echo $this->_tpl_vars['article']['href']; ?>
"><?php echo $this->_tpl_vars['article']['title']; ?>
</a>
		<?php if ($this->_tpl_vars['cfg']['showdate']): ?><div class="mod_latest_date"><a href="<?php echo $this->_tpl_vars['article']['authorhref']; ?>
"><?php echo $this->_tpl_vars['article']['author']; ?>
</a> &mdash; <?php echo $this->_tpl_vars['article']['date']; ?>
</div><?php endif; ?>

        <?php if ($this->_tpl_vars['cfg']['showdesc']): ?>
            <div class="mod_latest_desc" style="overflow:hidden">
                <?php if ($this->_tpl_vars['article']['image']): ?>
                    <div class="con_image" style="float:left;margin-top:12px;margin-right:16px;margin-bottom:16px">
                        <a href="<?php echo $this->_tpl_vars['article']['href']; ?>
" title="<?php echo $this->_tpl_vars['article']['title']; ?>
">
                            <img src="/images/photos/small/<?php echo $this->_tpl_vars['article']['image']; ?>
" border="0" alt="<?php echo $this->_tpl_vars['article']['title']; ?>
"/>
                        </a>
                    </div>
                <?php endif; ?>
                <?php echo $this->_tpl_vars['article']['description']; ?>

            </div>
        <?php endif; ?>

        <?php if ($this->_tpl_vars['cfg']['showcom']): ?>
            <div class="mod_latest_comments"><a href="<?php echo $this->_tpl_vars['article']['href']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['article']['comments'])) ? $this->_run_mod_handler('spellcount', true, $_tmp, 'комментарий', 'комментария', 'комментариев') : smarty_modifier_spellcount($_tmp, 'комментарий', 'комментария', 'комментариев')); ?>
</a></div>
        <?php endif; ?>
	</div>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['cfg']['showrss']): ?>
	<div class="mod_latest_rss">
		<a href="/rss/content/<?php echo $this->_tpl_vars['rssid']; ?>
/feed.rss">Лента материалов</a>
	</div>
<?php endif; ?>