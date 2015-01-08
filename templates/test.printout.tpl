<h1>Testing printout</h1>
<h2>Variables</h2>
<b>$test_name: </b><{$test_name}><br>
<b>$test_number: </b><{$test_number}><br>
<b>$test_array.first: </b><{$test_array.first}><br>
<b>$test_array[first]: </b><{$test_array[first]}><br>
<b>$test_array[$test_name]: </b><{$test_array[$test_name]}><br>
<b>$test_array[4].subarray: </b><{$test_array[4].subarray}><br>
<b>$test_array[$test_number].subarray: </b><{$test_array[$test_number].subarray}><br>
<b>$test_array[4].testing[3]: </b><{$test_array[4].testing[3]}><br>
<b>$test_array.foo.bar: </b><{$test_array.foo.bar}><br>
<b>$test_obj->v1: </b><{$test_obj->v1}><br>
<b>$test_obj->f1(): </b><{$test_obj->f1()}><br>
<b>"Content of test_name is "|cat:$test_name: </b><{"Content of test_name is "|cat:$test_name}><br>


<h2>Modifiers</h2>
<b>$test_name|upper: </b><{$test_name|upper}><br>
<b>$test_number|upper: </b><{$test_number|upper}><br>
<b>$test_array.first|upper: </b><{$test_array.first|upper}><br>
<b>$test_array[first]|upper: </b><{$test_array[first]|upper}><br>
<b>$test_array[$test_name]|upper: </b><{$test_array[$test_name]|upper}><br>
<b>$test_array[4].subarray|upper: </b><{$test_array[4].subarray|upper}><br>
<b>$test_array[$test_number].subarray|upper: </b><{$test_array[$test_number].subarray|upper}><br>
<b>$test_array[4].testing[3]|upper: </b><{$test_array[4].testing[3]|upper}><br>
<b>$test_array.foo.bar|upper: </b><{$test_array.foo.bar|upper}><br>
<b>$test_obj->v1|upper: </b><{$test_obj->v1|upper}><br>
<b>$test_obj->f1()|upper: </b><{$test_obj->f1()|upper}><br>

<b>$test_name|count_characters: </b><{$test_name|count_characters}><br>
<b>$test_name|count_characters:true: </b><{$test_name|count_characters:true}><br>


<hr>


