<?php
$config['db_table_prefix']='';

if (!defined('CONTEXT_HOME')) {
    define('CONTEXT_HOME',0);
}
if (!defined('CONTEXT_SYSTEM')) {
    define('CONTEXT_SYSTEM',10);
}
if (!defined('CONTEXT_USER')) {
    define('CONTEXT_USER',20);
}
if (!defined('CONTEXT_MODULE')) {
    define('CONTEXT_MODULE',30);
}
if (!defined('CONTEXT_SUBMODULE')) {
    define('CONTEXT_SUBMODULE',40);
}

$config['menu_positions']=array('left-bar','top','bottom','mini-top','status');

// used to defined the mode of installation.
$config['permission']['permissions_mode']='weight';//role,weight

//default role for everybody in home
$config['default-role']='visitor';

$config['roles']=array(
        array('name'=>'Superuser','weight'=>50,'shortname'=>'super','description'=>''),
        array('name'=>'Administrator','weight'=>40,'shortname'=>'admin','description'=>''),
        array('name'=>'Editing Teacher','weight'=>30,'shortname'=>'teacheredt','description'=>''),
        array('name'=>'Teacher','weight'=>20,'shortname'=>'teacher','description'=>''),
        array('name'=>'Student','weight'=>10,'shortname'=>'student','description'=>'')
        );

$config['capabilities']=array(
        'auth/view' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME),
        'auth/add' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME),
        'auth/enrolments' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME),
        'school/system' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME),
        'school/plan' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME),
        'school/subjects' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME),
        );