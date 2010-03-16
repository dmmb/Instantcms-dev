<?php /* Smarty version 2.6.19, created on 2010-03-16 15:56:02
         compiled from mod_auth.tpl */ ?>
<form action="/login" method="post" name="authform" style="margin:0px" target="_self" id="authform">
    <table class="authtable" width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td width="86"><?php echo $this->_tpl_vars['LANG']['AUTH_LOGIN']; ?>
:</td>
            <td width="917"><input name="login" type="text" id="login" size="15" /></td>
        </tr>
        <tr>
            <td height="30" valign="top"><?php echo $this->_tpl_vars['LANG']['AUTH_PASS']; ?>
:</td>
            <td valign="top"><input name="pass" type="password" id="pass" size="15" /></td>
        </tr>
        <tr>
            <td height="27" colspan="2" align="right" valign="top">
                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr style="font-size:10px">
                        <td width="13%"><input style="width:60px" type="submit" name="Submit" value="<?php echo $this->_tpl_vars['LANG']['AUTH_ENTER']; ?>
" /></td>
                        <td width="87%" align="right">
                            <?php if ($this->_tpl_vars['cfg']['autolog']): ?>
                                <input name="remember" type="checkbox" id="remember" value="1"  style="margin-right:0px"/> <?php echo $this->_tpl_vars['LANG']['AUTH_REMEMBER']; ?>
<br />
                            <?php endif; ?>
                            <?php if ($this->_tpl_vars['cfg']['passrem']): ?>
                                <a href="/0/passremind.html"><?php echo $this->_tpl_vars['LANG']['AUTH_FORGOT']; ?>
</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>