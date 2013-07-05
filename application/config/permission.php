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
/*define('CONTEXT_SYSTEM', 10);
define('CONTEXT_USER', 20);
define('CONTEXT_MODULE', 30);
define('CONTEXT_SUBMODULE', 40);*/


$config['menu_positions']=array('left-bar','top','bottom','mini-top','status');

$config['context']=array();
$config['default-roles']=array(
        array('name'=>'Superuser','weight'=>50,'shortname'=>'super','description'=>''),
        array('name'=>'Administrator','weight'=>40,'shortname'=>'admin','description'=>''),
        array('name'=>'Editing Teacher','weight'=>30,'shortname'=>'teacheredt','description'=>''),
        array('name'=>'Teacher','weight'=>20,'shortname'=>'teacher','description'=>''),
        array('name'=>'Student','weight'=>10,'shortname'=>'student','description'=>'')
        );

$config['guest-capabilities']=array(
        'auth/login' => array('visible'=>true,'position'=>'status'),
        'auth/register' => array(),
        'auth/send_again' => array(),
        'auth/activate' => array(),
        'auth/forgot_password' => array(),
        'auth/reset_password' => array(),
        'auth/change_password' => array(),
        'auth/change_email' => array(),
        'auth/reset_password' => array(),
        'auth/reset_email' => array(),
        'auth/unregister' => array(),
        );

$config['default-capabilities']=array(
        'auth/view' => array('weight'=>30, 'visible'=>true,'position'=>'left-bar','ctx_level'=>CONTEXT_SYSTEM),
        'auth/add' => array('weight'=>30,'ctx_level'=>CONTEXT_SYSTEM),
        'auth/enrolments' => array('weight'=>30,'ctx_level'=>CONTEXT_SYSTEM),
        );


//NOTAS
// en las capacidades la posicion solo se usaria si el menu es visible y es un valor por defecto para 
// al asignar los menus a los roles en un contexto indicar en que posicion debe ponerse
//permitiendo mover los menus entre las locaciones que se consideren