<?php

/**
 * Kiaan framework
 *
 * @author Hassan Kerdash kerdashhassan@gmail.com
 **/

/*
|---------------------------------------------------
| Uses
|---------------------------------------------------
*/
use Kiaan\Variable;
use Kiaan\Url;
use Kiaan\View;
use Kiaan\Http\Input as InputForm;
use Kiaan\Input;
use Kiaan\Route;
use Kiaan\Validator;
use Kiaan\Trans;
use Kiaan\Config;

#--------------------------------------------------
/**
 * Dump and die
 *
 */
if (!function_exists('dd')) {
    function dd($data) {
        return Variable::dd($data);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Get WWW
 *
 */
if (!function_exists('www')) {
    function www($url='') {
        return Url::www($url);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Public
 *
 */
if (!function_exists('publicWww')) {
    function publicWww($path='') {
        return Url::public($path);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Asset
 *
 */
if (!function_exists('asset')) {
    function asset($path='') {
        return Url::public($path);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Get root
 *
 */
if (!function_exists('root')) {
    function root($path='') {
        return Url::root($path);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Public root
 *
 */
if (!function_exists('publicRoot')) {
    function publicRoot($path='') {
        return Url::publicRoot($path);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Go
 *
 */
if (!function_exists('go')) {
    function go($url='') {
        return Url::go(Url::www($url));
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Go to URL
 *
 */
if (!function_exists('goUrl')) {
    function goUrl($url) {
        return Url::go($url);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Go back
 *
 */
if (!function_exists('back')) {
    function back() {
        return Url::goBack();
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Page
 *
 */
if (!function_exists('page')) {
    function page($path, $data=[]) {
        return View::page($path, $data);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Form
 *
 */
if (!function_exists('form')) {
    function form() {
        return new InputForm(Input::getRootPath());
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Validate
 *
 */
if (!function_exists('validate')) {
    function validate(array $request, array $rules, array $message = null) {
        return Validator::validate($request, $rules, $message);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Route
 *
 */
if (!function_exists('route')) {
    function route($name) {
       return Route::url($name);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Go route
 *
 */
if (!function_exists('goRoute')) {
    function goRoute($name) {
        return Url::go(Route::url($name));
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Trans
 *
 */
if (!function_exists('trans')) {
    function trans($content, $key, $vars=[], $default=null, $lang=null) {
        return Trans::get($content, $key, $vars, $default, $lang) ;
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Config
 *
 */
if (!function_exists('config')) {
    function config($content, $key, $vars=[], $default=null) {
        return Config::get($content, $key, $vars, $default);
    }
}
#--------------------------------------------------

