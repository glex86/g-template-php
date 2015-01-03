<?php
function get_microtime()
{
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = (double)($mtime[1]) + (double)($mtime[0]);
    return ($mtime);
}
$time = get_microtime();
require_once 'lib/class.template.php';

$tpl = new gTemplate();
$tpl->left_delimiter = '<{';
$tpl->right_delimiter = '}>';
$tpl->force_compile = true;

$tpl->assign('glex', '<?php echo "xx"; ?>');
$tpl->assign('data', 'ingatlanok.html');

$tpl->assign("Name","Fred Irving Johnathan Bradley Peppergill");
$tpl->assign("FirstName",array("John","Mary","James","Henry"));
$tpl->assign("LastName",array("Doe","Smith","Johnson","Case"));
$tpl->assign("Class",array(array("A","B","C","D"), array("E", "F", "G", "H"),
      array("I", "J", "K", "L"), array("M", "N", "O", "P")));

$tpl->assign("contacts", array(array("phone" => "1", "fax" => "2", "cell" => "3"),
      array("phone" => "555-4444", "fax" => "555-3333", "cell" => "760-1234")));

$tpl->assign("option_values", array("NY","NE","KS","IA","OK","TX"));
$tpl->assign("option_output", array("New York","Nebraska","Kansas","Iowa","Oklahoma","Texas"));
$tpl->assign("option_selected", "NE");

$tpl->assign('header', 'My Post Comments');

$comments = array(array( 'name'=>'Joe', 'body'=>'Thanks for this post!' ),
            array( 'name'=>'Sam', 'body'=>'Thanks for this post!' ),
            array( 'name'=>'Heather', 'body'=>'Thanks for this post!' ),
            array( 'name'=>'Kathy', 'body'=>'Thanks for this post!' ),
            array( 'name'=>'George', 'body'=>'Thanks for this post!' )
            );

$tpl->assign('comments', $comments);

    for($x = 0; $x < 50; $x++) {
        $tpl->assign('foo_'.$x, 'bar_'.$x);
    }

    for($x = 0; $x < 50; $x++) {
        $foo[] = 'bar_'.$x;
    }

    $tpl->assign('foo',$foo);

$tpl->display('index.tpl');
$runTime = get_microtime()-$time;
error_log($_GET['s'].';'.$runTime.';'.memory_get_usage(true).';'.memory_get_peak_usage(true)."\n", 3, __FILE__.'.csv');
