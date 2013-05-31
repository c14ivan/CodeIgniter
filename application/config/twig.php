<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['twig']['template_dir'] = APPPATH.'views';

/*
 |--------------------------------------------------------------------------
| Twig Cache Dir
|--------------------------------------------------------------------------
|
| Path to the cache folder for compiled twig templates. It is relative to
| CodeIgniter's base directory.
|
*/
$config['twig']['cache_dir'] = APPPATH.'cache/twig/';
/*
 |--------------------------------------------------------------------------
| Template file extension
|--------------------------------------------------------------------------
|
| This lets you define the extension for template files. It doesn't affect
| how Twiggy deals with templates but this may help you if you want to
| distinguish different kinds of templates. For example, for CodeIgniter
| you may use *.html.twig template files and *.html.jst for js templates.
|
*/
$config['twig']['template_file_ext'] = '.html.twig';

/*
 |--------------------------------------------------------------------------
| Syntax Delimiters
|--------------------------------------------------------------------------
|
| If you don't like the default Twig syntax delimiters or if they collide
| with other languages (for example, you use handlebars.js in your
        | templates), here you can change them.
|
| Ruby erb style:
|
|	'tag_comment' 	=> array('<%#', '#%>'),
|	'tag_block'   	=> array('<%', '%>'),
|	'tag_variable'	=> array('<%=', '%>')
|
| Smarty style:
|
|    'tag_comment' 	=> array('{*', '*}'),
|    'tag_block'   	=> array('{', '}'),
|    'tag_variable'	=> array('{$', '}'),
|
*/

$config['twig']['delimiters'] = array
(
        'tag_comment' 	=> array('{#', '#}'),
        'tag_block'   	=> array('{%', '%}'),
        'tag_variable'	=> array('{{', '}}')
);
/*
 |--------------------------------------------------------------------------
| Environment Options
|--------------------------------------------------------------------------
|
| These are all twig-specific options that you can set. To learn more about
| each option, check the official documentation.
|
| NOTE: cache option works slightly differently than in Twig. In Twig you
| can either set the value to FALSE to disable caching, or set the path
| to where the cached files should be stored (which means caching would be
        | enabled in that case). This is not entirely convenient if you need to
| switch between enabled or disabled caching for debugging or other reasons.
|
| Therefore, here the value can be either TRUE or FALSE. Cache directory
| can be set separately.
|
*/

$config['twig']['environment']['cache']              	= FALSE;
$config['twig']['environment']['debug']              	= TRUE;
$config['twig']['environment']['charset']            	= 'utf-8';
$config['twig']['environment']['base_template_class']	= 'Twig_Template';
$config['twig']['environment']['auto_reload']        	= NULL;
$config['twig']['environment']['strict_variables']   	= FALSE;
$config['twig']['environment']['autoescape']         	= FALSE;
$config['twig']['environment']['optimizations']      	= -1;

/*
 |--------------------------------------------------------------------------
| Default layout
|--------------------------------------------------------------------------
*/

$config['twig']['default_layout'] = 'index';

/*
 |--------------------------------------------------------------------------
| Auto-reigster functions
|--------------------------------------------------------------------------
|
| Here you can list all the functions that you want Twiggy to automatically
| register them for you.
|
| NOTE: only registered functions can be used in Twig templates.
|
*/

$config['twig']['register_functions'] = array
(
        'css','img_url','img','site_url','asset_url','css_url','less_url','js_url','js','base_url','current_url'
);

/*
 |--------------------------------------------------------------------------
| Auto-reigster filters
|--------------------------------------------------------------------------
|
| Much like with functions, list filters that you want Twiggy to
| automatically register them for you.
|
| NOTE: only registered filters can be used in Twig templates. Also, keep
| in mind that a filter is nothing more than just a regular function that
| acceps a string (value) as a parameter and outputs a modified/new string.
|
*/

$config['twig']['register_filters'] = array
(

);