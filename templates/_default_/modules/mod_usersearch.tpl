<script src="/includes/jquery/autocomplete/jquery.autocomplete.min.js" type="text/javascript"></script>
<link media="screen" rel="stylesheet" href="/includes/jquery/autocomplete/jquery.autocomplete.css" type="text/css">

<form name="usr_search_form" method="post" action="/users/{$cfg.menuid}/search.html">
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
        <tr>
            <td valign="middle" style="text-align:center">
                �����
                <select name="gender" id="gender" style="width:150px">
                    <option value="f">������</option>
                    <option value="m">������</option>
                    <option value="0" selected>����</option>
                </select>
            </td>
        </tr>
        <tr>
            <td style="text-align:center">
                ��
                <input style="text-align:center;width:60px" name="agefrom" type="text" id="agefrom" value="18"/>
                ��
                <input style="text-align:center;width:60px" name="ageto" type="text" id="ageto" value=""/>
                ���
            </td>
        </tr>
        <tr>
            <td style="text-align:center">
                ��� 
                <input style="text-align:center;width:158px" id="name" name="name" type="text" value=""/>
            </td>
        </tr>
        <tr>
            <td style="text-align:center">
                ����� 
                <input style="text-align:center;width:150px" id="city" name="city" type="text" value=""/>
                <script type="text/javascript">
                    {$autocomplete_js}
                </script>
            </td>
        </tr>
        <tr>
            <td style="text-align:center">
                �������� 
                <input style="text-align:center;width:128px" id="hobby" name="hobby" type="text" value=""/>
            </td>
        </tr>
        <tr>
            <td align="center">
                <input name="gosearch" type="submit" id="gosearch" value="�����!" />
            </td>
        </tr>
    </table>
</form>