<h1>Testing Section</h1>
<pre>
<{section name=outer loop=$FirstName}>
<{if $smarty.section.outer.index is odd by 2}>
    <{%outer.iteration%}> . <{$FirstName[outer]}> <{$LastName[outer]}>
<{else}>
    <{$smarty.section.outer.iteration}> * <{$FirstName[outer]}> <{$LastName[outer]}>
<{/if}>
<{sectionelse}>
    none
<{/section}>
</pre>

<{section name=sec1 loop=$contacts}>
<fieldset>
<{if %sec1.first%}><legend>First</legend><{/if}>
<{if %sec1.last%}><legend>Last</legend><{/if}>
    phone: <{$contacts[sec1].phone}><br>
    fax: <{$contacts[sec1].fax}><br>
    cell: <{$contacts[sec1].cell}><br>
</fieldset>
<{/section}>

