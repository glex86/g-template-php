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
$tpl->force_compile = false;

class testclass {
    public $v1 = 'variable 1';
    public $true = true;
    public $false = false;

    function f1() {
        return 'Result of function f1';
    }
}

$tpl->assign('Name', 'G-Template Engine');
$tpl->assign('test_name', 'Dávid Tamás');
$tpl->assign('test_number', 4);
$tpl->assign('test_array', array('first'=>'First item', 'second'=>'Second item', '3'=>'Third item', 'Dávid Tamás'=>'my name is Dávid Tamás', '4'=>array('subarray'=>'Great', 'testing'=>array('3'=>'subtesting')), 'foo'=>array('bar'=>'this is foobar')));
$tpl->assign('test_obj', new testclass());

$tpl->assign('test_true', true);
$tpl->assign('test_false',false);
$tpl->assign('valami', '');

$tpl->assign("FirstName",array("John","Mary","James","Henry", 'Tamas', 'G'));
$tpl->assign("LastName",array("Doe","Smith","Johnson","Case", 'David', 'Lex'));

$tpl->assign("contacts", array(
                                array("phone" => "555-1111", "fax" => "666-1111", "cell" => "760-1111"),
                                array("phone" => "555-2222", "fax" => "666-2222", "cell" => "760-2222"),
                                array("phone" => "555-3333", "fax" => "666-3333", "cell" => "760-3333"),
                                array("phone" => "555-4444", "fax" => "666-4444", "cell" => "760-4444"),
                                array("phone" => "555-5555", "fax" => "666-5555", "cell" => "760-5555"),
                                array("phone" => "555-6666", "fax" => "666-6666", "cell" => "760-6666"),
                            ));

//$tpl->display('test.tpl');
//exit;


$tpl->assign('simpleSelect', array('6'=>'Item 1', '8'=>'Item 2', '10'=>'Item 3', '12'=>'Item 4'));
$tpl->assign('grouppedSelect', array('Group 1'=>array('6'=>'Item 1', '8'=>'Item 2', '10'=>'Item 3', '12'=>'Item 4'), 'Group 2'=>array('5'=>'Item 1', '7'=>'Item 2', '9'=>'Item 3', '11'=>'Item 4')));


$tpl->assign('glex', '<?php echo "xx"; ?>');
$tpl->assign('data', 'ingatlanok.html');


$tpl->assign('glexobj', new testclass());


$tpl->assign("Class",array(array("A","B","C","D"), array("E", "F", "G", "H"),
      array("I", "J", "K", "L"), array("M", "N", "O", "P")));

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
