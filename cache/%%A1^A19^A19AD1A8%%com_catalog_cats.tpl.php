<?php /* Smarty version 2.6.19, created on 2010-08-09 21:22:12
         compiled from com_catalog_cats.tpl */ ?>

<ul class="uc_cat_list">
	<?php $_from = $this->_tpl_vars['cats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['cat']):
?>	
		<li class="uc_cat_item"><a href="/catalog/<?php echo $this->_tpl_vars['cat']['id']; ?>
"><?php echo $this->_tpl_vars['cat']['title']; ?>
</a> (<?php echo $this->_tpl_vars['cat']['content_count']; ?>
)</li>
	<?php endforeach; endif; unset($_from); ?>
</ul>