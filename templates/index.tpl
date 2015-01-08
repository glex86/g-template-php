<{config_load file="test.conf"}>
<{include file="header.tpl" title=foo}>

<{$valami|default:"Valami default"}>

<h1><{$glexobj->v1}></h1>

<PRE>
<{literal}>
function myJsFunction(name, ip){
   alert("The server name\n" + name + "\n" + ip);' sd s
}

<{/literal}>
<?php echo 1; ?>

<{* bold and title are read from the config file *}>
<{if #bold#}><b><{/if}>
<{* capitalize the first letters of each word of the title *}>
Title: <{#title#|capitalize}>
<{if #bold#}></b><{/if}>

The current date and time is <{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}>

The value of global assigned variable $SCRIPT_NAME is <{$SCRIPT_NAME}>

Example of accessing server environment variable SERVER_NAME: <{$smarty.server.SERVER_NAME}>

The value of <{ldelim}>$Name<{rdelim}> is <b><{$Name}></b>

variable modifier example of <{ldelim}>$Name|upper<{rdelim}>

<b><{$Name|upper}></b>

An example of a section loop:

<{section name=outer loop=$FirstName}>
<{if $smarty.section.outer.index is odd by 2}>
    <{%outer.index%}> . <{$FirstName[outer]}> <{$LastName[outer]}>
<{else}>
    <{$smarty.section.outer.index}> * <{$FirstName[outer]}> <{$LastName[outer]}>
<{/if}>
<{sectionelse}>
    none
<{/section}>

An example of section looped key values:

<{section name=sec1 loop=$contacts}>
    phone: <{$contacts[sec1].phone}><br>
    fax: <{$contacts[sec1].fax}><br>
    cell: <{$contacts[sec1].cell}><br>
<{/section}>
<p>

An example of foreach looped key values:
<p>
<{foreach from=$contacts item=item name=alfa}>
    phone1: <{$item.phone}><br>
    fax1: <{$item.fax}><br>
    cell1: <{$item.cell}><br>
<{foreachelse}>
hopp
<{/foreach}>

</p>

testing strip tags
<{strip}>
<table border=0>
    <tr>
        <td>
            <A HREF="<{$SCRIPT_NAME}>">
            <font color="red">This is a  test     </font>
            </A>
        </td>
    </tr>
</table>
<{/strip}>

</PRE>



This is an example of the html_options function:

<form>
<select name=states>
<{html_options values=$option_values selected=$option_selected output=$option_output}>
</select>
</form>

<div class="comments">
    <h3><{$header}></h3>
    <ul>
        <{foreach from=$comments  item=comment}>
        <li class="comment">
            <h5><{$comment.name}></h5>
            <p><{$comment.body}></p>
        </li>
        <{/foreach}>
    </ul>
</div>

<{* df dsf fg
 gdf gdfg *}>

abcdefghijklmnopqrstuvwxyz
<{$foo_0}>
abcdefghijklmnopqrstuvwxyz
<{$foo_1}>
abcdefghijklmnopqrstuvwxyz
<{$foo_2}>
abcdefghijklmnopqrstuvwxyz
<{$foo_3}>
abcdefghijklmnopqrstuvwxyz
<{$foo_4}>
abcdefghijklmnopqrstuvwxyz
<{$foo_5}>
abcdefghijklmnopqrstuvwxyz
<{$foo_6}>
abcdefghijklmnopqrstuvwxyz
<{$foo_7}>
abcdefghijklmnopqrstuvwxyz
<{$foo_8}>
abcdefghijklmnopqrstuvwxyz
<{$foo_9}>
abcdefghijklmnopqrstuvwxyz
<{$foo_10}>
abcdefghijklmnopqrstuvwxyz
<{$foo_11}>
abcdefghijklmnopqrstuvwxyz
<{$foo_12}>
abcdefghijklmnopqrstuvwxyz
<{$foo_13}>
abcdefghijklmnopqrstuvwxyz
<{$foo_14}>
abcdefghijklmnopqrstuvwxyz
<{$foo_15}>
abcdefghijklmnopqrstuvwxyz
<{$foo_16}>
abcdefghijklmnopqrstuvwxyz
<{$foo_17}>
abcdefghijklmnopqrstuvwxyz
<{$foo_18}>
abcdefghijklmnopqrstuvwxyz
<{$foo_19}>
abcdefghijklmnopqrstuvwxyz
<{$foo_20}>
abcdefghijklmnopqrstuvwxyz
<{$foo_21}>
abcdefghijklmnopqrstuvwxyz
<{$foo_22}>
abcdefghijklmnopqrstuvwxyz
<{$foo_23}>
abcdefghijklmnopqrstuvwxyz
<{$foo_24}>
abcdefghijklmnopqrstuvwxyz
<{$foo_25}>
abcdefghijklmnopqrstuvwxyz
<{$foo_26}>
abcdefghijklmnopqrstuvwxyz
<{$foo_27}>
abcdefghijklmnopqrstuvwxyz
<{$foo_28}>
abcdefghijklmnopqrstuvwxyz
<{$foo_29}>
abcdefghijklmnopqrstuvwxyz
<{$foo_30}>

abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
<{section name=bar loop=$foo}>
    abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
    <{$foo[bar]}>
    abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
<{/section}>
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz
abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz

abcdefghijklmnopqrstuvwxyz
<{$foo_31}>
abcdefghijklmnopqrstuvwxyz
<{$foo_32}>
abcdefghijklmnopqrstuvwxyz
<{$foo_33}>
abcdefghijklmnopqrstuvwxyz
<{$foo_34}>
abcdefghijklmnopqrstuvwxyz
<{$foo_35}>
abcdefghijklmnopqrstuvwxyz
<{$foo_36}>
abcdefghijklmnopqrstuvwxyz
<{$foo_37}>
abcdefghijklmnopqrstuvwxyz
<{$foo_38}>
abcdefghijklmnopqrstuvwxyz
<{$foo_39}>
abcdefghijklmnopqrstuvwxyz
<{$foo_40}>
abcdefghijklmnopqrstuvwxyz
<{$foo_41}>
abcdefghijklmnopqrstuvwxyz
<{$foo_42}>
abcdefghijklmnopqrstuvwxyz
<{$foo_43}>
abcdefghijklmnopqrstuvwxyz
<{$foo_44}>
abcdefghijklmnopqrstuvwxyz
<{$foo_45}>
abcdefghijklmnopqrstuvwxyz
<{$foo_46}>
abcdefghijklmnopqrstuvwxyz
<{$foo_47}>
abcdefghijklmnopqrstuvwxyz
<{$foo_48}>
abcdefghijklmnopqrstuvwxyz
<{$foo_49|json_encode}>

<{include file="test.tpl"}>

<{include file="footer.tpl"}>

