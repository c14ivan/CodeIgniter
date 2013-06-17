<?php

$config['context_levels'] = array(10=>'CONTEXT_SYSTEM',20=>'CONTEXT_USER',30=>'CONTEXT_MODULE',40=>'CONTEXT_SUBMODULE');

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
