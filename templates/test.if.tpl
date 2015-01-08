<h1>Testing IF</h1>
<h2>Bool variables</h2>
<b>$test_true: </b><{if $test_true}>Good<{else}>Bad<{/if}><br>
<b>$test_false: </b><{if $test_false}>Bad<{else}>Good<{/if}><br>
<b>!$test_false: </b><{if !$test_false}>Good<{else}>Bad<{/if}><br>
<b>!$test_true: </b><{if !$test_true}>Bad<{else}>Good<{/if}><br>
<b>!$test_true: </b><{if !$test_true}>Bad<{else}>Good<{/if}><br>
<b>$test_true == true: </b><{if $test_true == true}>Good<{else}>Bad<{/if}><br>
<b>$test_true == false: </b><{if $test_true == false}>Bad<{else}>Good<{/if}><br>

<b>$test_obj->true: </b><{if $test_obj->true}>Good<{else}>Bad<{/if}><br>
<b>$test_obj->false: </b><{if $test_obj->false}>Bad<{else}>Good<{/if}><br>
<b>!$test_obj->false: </b><{if !$test_obj->false}>Good<{else}>Bad<{/if}><br>
<b>!$test_obj->true: </b><{if !$test_obj->true}>Bad<{else}>Good<{/if}><br>
<b>!$test_obj->true: </b><{if !$test_obj->true}>Bad<{else}>Good<{/if}><br>
<b>$test_obj->true == true: </b><{if $test_obj->true == true}>Good<{else}>Bad<{/if}><br>
<b>$test_obj->true == false: </b><{if $test_obj->true == false}>Bad<{else}>Good<{/if}><br>


<h2>String variables</h2>
<b>$test_name: </b> <{if $test_name}>Good<{else}>Bad<{/if}><br>
<b>$test_name == 'Dávid Tamás': </b> <{if $test_name == 'Dávid Tamás'}>Good<{else}>Bad<{/if}><br>
<b>$test_obj->v1 == 'variable 1': </b> <{if $test_obj->v1 == 'variable 1'}>Good<{else}>Bad<{/if}><br>
<b>$test_obj->v1 == 'variable 2': </b> <{if $test_obj->v1 == 'variable 2'}>Bad<{else}>Good<{/if}><br>

<h2>Number variables</h2>
<b>$test_number: </b> <{if $test_number}>Good<{else}>Bad<{/if}><br>
<b>$test_number == 4: </b> <{if $test_number == 4}>Good<{else}>Bad<{/if}><br>
<b>$test_number !== 4: </b> <{if $test_number !== 4}>Bad<{else}>Good<{/if}><br>
<b>$test_number > 2: </b> <{if $test_number > 2}>Good<{else}>Bad<{/if}><br>
<b>$test_number >= 4: </b> <{if $test_number >= 4}>Good<{else}>Bad<{/if}><br>
<b>$test_number > 8: </b> <{if $test_number > 8}>Bad<{else}>Good<{/if}><br>
<b>$test_number < 2: </b> <{if $test_number < 2}>Bad<{else}>Good<{/if}><br>
<b>$test_number < 8: </b> <{if $test_number < 8}>Good<{else}>Bad<{/if}><br>
<b>$test_number <= 4: </b> <{if $test_number <= 4}>Good<{else}>Bad<{/if}><br>

<h2>Modifier results</h2>
<b>$test_name|count_characters == 10: </b> <{if $test_name|count_characters == 10}>Good<{else}>Bad<{/if}><br>
<b>$test_name|count_characters !== 10: </b> <{if $test_name|count_characters !== 10}>Bad<{else}>Good<{/if}><br>
<b>$test_name|count_characters > 9: </b> <{if $test_name|count_characters > 8}>Good<{else}>Bad<{/if}><br>
<b>$test_name|count_characters < 11: </b> <{if $test_name|count_characters < 11}>Good<{else}>Bad<{/if}><br>
<b>$test_name|count_characters < 9: </b> <{if $test_name|count_characters < 8}>Bad<{else}>Good<{/if}><br>
<b>$test_name|count_characters > 11: </b> <{if $test_name|count_characters > 11}>Bad<{else}>Good<{/if}><br>


<h2>Logic operators</h2>
<b>$test_false && $test_true: </b><{if $test_false && $test_true}>Bad<{else}>Good<{/if}><br>
<b>$test_false && $test_false: </b><{if $test_false && $test_false}>Bad<{else}>Good<{/if}><br>
<b>$test_true && $test_true: </b><{if $test_true && $test_true}>Good<{else}>Bad<{/if}><br>
<b>$test_false || $test_true: </b><{if $test_false || $test_true}>Good<{else}>Bad<{/if}><br>
<b>$test_false || $test_false: </b><{if $test_false || $test_false}>Bad<{else}>Good<{/if}><br>
<b>$test_true || $test_true: </b><{if $test_true || $test_true}>Good<{else}>Bad<{/if}><br>

<h2>Parenthesis</h2>
<b>(5 > 8) || (4 < 2): </b><{if (5 > 8) || (4 < 2)}>Bad<{else}>Good<{/if}><br>
<b>(5 < 8) || (4 < 2): </b><{if (5 < 8) || (4 < 2)}>Good<{else}>Bad<{/if}><br>
<b>!(5 > 8) || (4 < 2): </b><{if !(5 > 8) || (4 < 2)}>Good<{else}>Bad<{/if}><br>
