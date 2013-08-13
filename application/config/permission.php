<?php
$config['permission']['db_table_prefix']='';

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

$config['permission']['menu_positions']=array('left-bar','top','bottom','mini-top','status');

// used to defined the mode of installation.
$config['permission']['permissions_mode']='role';//role,weight

//default role for everybody in home
$config['permission']['default-role']='visitor';

$config['permission']['roles']=array(
        array('name'=>'Superuser','weight'=>50,'shortname'=>'super','description'=>''),
        array('name'=>'Administrator','weight'=>40,'shortname'=>'admin','description'=>''),
        array('name'=>'Editing Teacher','weight'=>30,'shortname'=>'teacheredt','description'=>''),
        array('name'=>'Teacher','weight'=>20,'shortname'=>'teacher','description'=>''),
        array('name'=>'Student','weight'=>10,'shortname'=>'student','description'=>'')
        );

$config['permission']['capabilities']=array(
        'user/view' => array('weight'=>30,'parent'=>'user_menu', 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin'),
        'user/add' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin'),
        'user/roles' => array('weight'=>30,'parent'=>'user_menu', 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_HOME,'roles'=>'super,admin'),
        'school/system' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME,'parent'=>'scsystem_menu','visible'=>true,'position'=>'left-bar','roles'=>'super'),
        'school/plan' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME,'parent'=>'scsystem_menu','visible'=>true,'position'=>'left-bar','roles'=>'super'),
        'school/subjects' => array('weight'=>30,'ctx_level'=>CONTEXT_HOME,'parent'=>'scsystem_menu','visible'=>true,'position'=>'left-bar','roles'=>'super'),
        'library/admin' =>array('weight'=>30,'ctx_level'=>CONTEXT_HOME,'parent'=>'library_menu','visible'=>true,'position'=>'left-bar','roles'=>'super'),
        );