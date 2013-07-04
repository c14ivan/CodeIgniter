<?php
$config['db_table_prefix']='';

$config['context_levels'] = array('CONTEXT_SYSTEM'=>10,'CONTEXT_USER'=>20,'CONTEXT_MODULE'=>30,'CONTEXT_SUBMODULE'=>40);
$config['menu_positions']=array('left-bar','top','bottom','mini-top');

$config['context']=array();
$config['default-roles']=array(
        array('name'=>'Superuser','weight'=>50,'shortname'=>'super','description'=>''),
        array('name'=>'Administrator','weight'=>40,'shortname'=>'admin','description'=>''),
        array('name'=>'Editing Teacher','weight'=>30,'shortname'=>'teacheredt','description'=>''),
        array('name'=>'Teacher','weight'=>20,'shortname'=>'teacher','description'=>''),
        array('name'=>'Student','weight'=>10,'shortname'=>'student','description'=>'')
        );

$config['guest-capabilities']=array(
        array('capability'=>'auth/login','url'=>'auth/login','visible'=>false),
        array('capability'=>'auth/register','url'=>'auth/register','visible'=>false),
        array('capability'=>'auth/send_again','url'=>'auth/send_again','visible'=>false),
        array('capability'=>'auth/activate','url'=>'auth/activate','visible'=>false),
        array('capability'=>'auth/forgot_password','url'=>'auth/forgot_password','visible'=>false),
        array('capability'=>'auth/reset_password','url'=>'auth/reset_password','visible'=>false),
        array('capability'=>'auth/change_password','url'=>'auth/change_password','visible'=>false),
        array('capability'=>'auth/change_email','url'=>'auth/change_email','visible'=>false),
        array('capability'=>'auth/reset_password','url'=>'auth/reset_password','visible'=>false),
        array('capability'=>'auth/reset_email','url'=>'auth/reset_email','visible'=>false),
        array('capability'=>'auth/unregister','url'=>'auth/unregister','visible'=>false),
        );

$config['default-capabilities']=array(
        array('capability'=>'auth/view','url'=>'auth/view','weight'=>30, 'visible'=>true,'position'=>'left-bar'),
        array('capability'=>'auth/add','url'=>'auth/add','weight'=>30, 'visible'=>false),
        array('capability'=>'auth/enrolments','url'=>'auth/enrolments','weight'=>30),
        );



//TODO: implement this
//al instalar:
//1. crear contexto home, debe tener context level 10 e instanceid=0
//2. crear los roles por defecto
//3. crear las capabilities en DB
//4. crear las capacidades de los roles, comparando los pesos de los roles con los pesos de las capacidades y 
//   que esten en el context_level 0, por defecto quiero crear capacidades para el home
//5. crear un usuario y asignar el role superadministrador para el contexto home. 


//NOTAS
// en las capacidades la posicion solo se usaria si el menu es visible y es un valor por defecto para 
// al asignar los menus a los roles en un contexto indicar en que posicion debe ponerse
//permitiendo mover los menus entre las locaciones que se consideren