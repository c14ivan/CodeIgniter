<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Twiggy - Twig template engine implementation for CodeIgniter
 *
 * @author 		Edmundas Kondrašovas <as@edmundask.lt>
 * @license		http://www.opensource.org/licenses/MIT
*/

$autoload['libraries'] = array('twiggy','rb');
$autoload['config'] = array('twiggy','asset');
$autoload['helper'] = array('form','url','asset','path','twiggy');