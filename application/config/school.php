<?php
//Duración del ciclo de un sistema educativo
$config['school']['systemduration']=array(
		0=>lang('sc_durundefined'),
		1=>lang('sc_duryear'),
		2=>lang('sc_dursemester'),
		3=>lang('sc_durtrim'),
		4=>lang('sc_durbim'),
		5=>lang('sc_durmonth')
);
//status de un sistema
$config['school']['systemstatus']=array(
		0=>lang('sc_edition'),
		1=>lang('sc_inuse'),
		2=>lang('sc_deprecated')	
);
// estatus de las versiones de una asignatura
$config['school']['subjectversionstatus']=array(
		0=>lang('sc_edition'),
		1=>lang('sc_inuse'),
		2=>lang('sc_deprecated')
);
//metodos de enrolamiento: por ahora solo manual
$config['school']['enrolments']=array(
		'manual'=>lang('sc_enrolmanual'),
		//1=>lang('sc_enrolpass'),
);
//Status de los enrolamientos
$config['school']['enrolstatus']=array(
        0=>lang('status_disable'),
        1=>lang('status_able'),
);
//modo de enrolamiento: por ciclos o especificando materias
$config['school']['enrolmode']=array(
        1=>lang('scmode_cicles'),
        2=>lang('scmode_subjects'),
);

$config['school']['relatives']=array(
        'father'=>lang('relative_father'),
        'mother'=>lang('relative_mother'),
        'grand'=>lang('relative_grand'),
        'uncle'=>lang('relative_uncle'),
        'cousin'=>lang('relative_cousin'),
        'brother'=>lang('relative_brother'),
        'godfather'=>lang('relative_godfather'),
        'godmother'=>lang('relative_godmother'),
        'familyfriend'=>lang('relative_familyfriend'),
);