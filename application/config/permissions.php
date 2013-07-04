<?php

$config['context_levels'] = array(10=>'CONTEXT_SYSTEM',20=>'CONTEXT_USER',30=>'CONTEXT_MODULE',40=>'CONTEXT_SUBMODULE');

$config['context']=array();
$config['default-roles']=array(
        array('name'=>'Superuser','weight'=>50,'shortname'=>'super','description'=>''),
        array('name'=>'Administrator','weight'=>40,'shortname'=>'admin','description'=>''),
        array('name'=>'Editing Teacher','weight'=>30,'shortname'=>'teacheredt','description'=>''),
        array('name'=>'Teacher','weight'=>20,'shortname'=>'teacher','description'=>''),
        array('name'=>'Student','weight'=>10,'shortname'=>'student','description'=>'')
        );

$config['default-capabilities']=array(
        array('capability'=>'users/list','url'=>'auth/users','weight'=>30),
        array('capability'=>'users/add','url'=>'auth/users','weight'=>30),
        array('capability'=>'users/enrol','url'=>'','weight'=>30),
        );



//TODO: implement this
//al instalar:
//1. crear contexto home, debe tener context level 10 e instanceid=0
//2. crear los roles por defecto
//3. crear las capabilities en DB
//4. crear las capacidades de los roles, comparando los pesos de los roles con los pesos de las capacidades y 
//   que esten en el context_level 0, por defecto quiero crear capacidades para el home
//5. crear un usuario y asignar el role superadministrador para el contexto home. 