<{* gTemplate debug console *}>

<{if isset($_debug_output) and $_debug_output eq "html"}>
    <table border=0 width=100%>
    <tr bgcolor=#cccccc><th colspan=2>gTemplate Debug Console</th></tr>
    <tr bgcolor=#cccccc><td colspan=2><b>Included templates & config files (load time in seconds):</b></td></tr>
    <{foreach key=key item=templates from=$_debug_tpls}>
        <tr bgcolor=<{if $key % 2}>#eeeeee<{else}>#fafafa<{/if}>>
        <td colspan=2><tt><{section name=oo loop=$_debug_tpls[$key].depth}>&nbsp;&nbsp;&nbsp;<{/section}>
        <font color=<{if $_debug_tpls[$key].type eq "template"}>brown<{elseif $_debug_tpls[$key].type eq "insert"}>black<{else}>green<{/if}>>
        <{$_debug_tpls[$key].filename}></font><{if isset($_debug_tpls[$key].exec_time)}>
        <font size=-1><i>(<{$_debug_tpls[$key].exec_time|string_format:"%.5f"}> seconds)<{if $key eq 0}> (total)<{/if}>
        </i></font><{/if}></tt></td></tr>
    <{foreachelse}>
        <tr bgcolor=#eeeeee><td colspan=2><tt><i>No template assigned</i></tt></td></tr>
    <{/foreach}>
    <tr bgcolor=#cccccc><td colspan=2><b>Assigned template variables:</b></td></tr>
    <{foreach key=key item=vars from=$_debug_keys}>
        <tr bgcolor=<{if $key % 2}>#eeeeee<{else}>#fafafa<{/if}>>
        <td valign=top><tt><font color=blue>{$<{$_debug_keys[$key]}>}</font></tt></td>
        <td nowrap><tt><font color=green><{$_debug_vals[$key]|debug_print_var}></font></tt></td></tr>
    <{foreachelse}>
        <tr bgcolor=#eeeeee><td colspan=2><tt><i>No template variables assigned</i></tt></td></tr>
    <{/foreach}>
    <tr bgcolor=#cccccc><td colspan=2><b>Assigned config file variables (outer template scope):</b></td></tr>
    <{foreach key=key item=config_vars from=$_debug_config_keys}>
        <tr bgcolor=<{if $key % 2}>#eeeeee<{else}>#fafafa<{/if}>>
        <td valign=top><tt><font color=maroon>{#<{$_debug_config_keys[$key]}>#}</font></tt></td>
        <td><tt><font color=green><{$_debug_config_vals[$key]|debug_print_var}></font></tt></td></tr>
    <{foreachelse}>
        <tr bgcolor=#eeeeee><td colspan=2><tt><i>No config vars assigned</i></tt></td></tr>
    <{/foreach}>
    </table>    
<{elseif $_debug_output == 'console'}>
    <SCRIPT language=javascript>
    console.info('gTemplate Debug Console');
    console.info('Included templates & config files (load time in seconds):');
    <{foreach key=key item=templates from=$_debug_tpls}>
        console.log('<{section name=oo loop=$_debug_tpls[$key].depth}>--<{/section}><{$_debug_tpls[$key].filename}>',
        '<{if isset($_debug_tpls[$key].exec_time)}><{$_debug_tpls[$key].exec_time|string_format:"%.5f"}> seconds<{if $key eq 0}> (total)<{/if}><{/if}>');
    <{foreachelse}>
        console.log('No template assigned');
    <{/foreach}>
        console.info('Assigned template variables:');
    <{foreach key=key item=vars from=$_debug_keys}>
        console.log('{$<{$_debug_keys[$key]}>}', <{$_debug_vals[$key]|json_encode}>);
    <{foreachelse}>
        console.log('No template variables assigned');
    <{/foreach}>
        console.info('Assigned config file variables (outer template scope):');
    <{foreach key=key item=config_vars from=$_debug_config_keys}>
        console.log('{#<{$_debug_config_keys[$key]}>#}', <{$_debug_config_vals[$key]|json_encode}>);
    <{foreachelse}>
        console.log('No config vars assigned');
    <{/foreach}>
</SCRIPT>
<{else}>
<SCRIPT language=javascript>    
    if( self.name == '' ) {
       var title = 'Console';
    }
    else {
       var title = 'Console_' + self.name;
    }    
    _gTemplate_console = window.open("",title.value,"width=680,height=600,resizable,scrollbars=yes");
    _gTemplate_console.document.write("<HTML><TITLE>gTemplate Debug Console_"+self.name+"</TITLE><BODY bgcolor=#ffffff>");
    _gTemplate_console.document.write("<table border=0 width=100%>");
    _gTemplate_console.document.write("<tr bgcolor=#cccccc><th colspan=2>gTemplate Debug Console</th></tr>");
    _gTemplate_console.document.write("<tr bgcolor=#cccccc><td colspan=2><b>Included templates & config files (load time in seconds):</b></td></tr>");
    <{foreach key=key item=templates from=$_debug_tpls}>
        _gTemplate_console.document.write("<tr bgcolor=<{if $key % 2}>#eeeeee<{else}>#fafafa<{/if}>>");
        _gTemplate_console.document.write("<td colspan=2><tt><{section name=oo loop=$_debug_tpls[$key].depth}>&nbsp;&nbsp;&nbsp;<{/section}>");
        _gTemplate_console.document.write("<font color=<{if $_debug_tpls[$key].type eq "template"}>brown<{elseif $_debug_tpls[$key].type eq "insert"}>black<{else}>green<{/if}>>");
        _gTemplate_console.document.write("<{$_debug_tpls[$key].filename}></font><{if isset($_debug_tpls[$key].exec_time)}> ");
        _gTemplate_console.document.write("<font size=-1><i>(<{$_debug_tpls[$key].exec_time|string_format:"%.5f"}> seconds)<{if $key eq 0}> (total)<{/if}>");
        _gTemplate_console.document.write("</i></font><{/if}></tt></td></tr>");
    <{foreachelse}>
        _gTemplate_console.document.write("<tr bgcolor=#eeeeee><td colspan=2><tt><i>No template assigned</i></tt></td></tr> ");
    <{/foreach}>
    _gTemplate_console.document.write("<tr bgcolor=#cccccc><td colspan=2><b>Assigned template variables:</b></td></tr>");
    <{foreach key=key item=vars from=$_debug_keys}>
        _gTemplate_console.document.write("<tr bgcolor=<{if $key % 2}>#eeeeee<{else}>#fafafa<{/if}>>");
        _gTemplate_console.document.write("<td valign=top><tt><font color=blue>{$<{$_debug_keys[$key]}>}</font></tt></td>");
        _gTemplate_console.document.write("<td nowrap><tt><font color=green><{$_debug_vals[$key]|debug_print_var}></font></tt></td></tr>");
    <{foreachelse}>
        _gTemplate_console.document.write("<tr bgcolor=#eeeeee><td colspan=2><tt><i>No template variables assigned</i></tt></td></tr>");
    <{/foreach}>
    _gTemplate_console.document.write("<tr bgcolor=#cccccc><td colspan=2><b>Assigned config file variables (outer template scope):</b></td></tr>");
    <{foreach key=key item=config_vars from=$_debug_config_keys}>
        _gTemplate_console.document.write("<tr bgcolor=<{if $key % 2}>#eeeeee<{else}>#fafafa<{/if}>>");
        _gTemplate_console.document.write("<td valign=top><tt><font color=maroon>{#<{$_debug_config_keys[$key]}>#}</font></tt></td>");
        _gTemplate_console.document.write("<td><tt><font color=green><{$_debug_config_vals[$key]|debug_print_var}></font></tt></td></tr>");
    <{foreachelse}>
        _gTemplate_console.document.write("<tr bgcolor=#eeeeee><td colspan=2><tt><i>No config vars assigned</i></tt></td></tr>");
    <{/foreach}>
    _gTemplate_console.document.write("</table>");
    _gTemplate_console.document.write("</BODY></HTML>");
    _gTemplate_console.document.close();
</SCRIPT>
<{/if}>
