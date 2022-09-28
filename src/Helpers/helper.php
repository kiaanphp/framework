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
use Kiaan\Input;
use Kiaan\Route;
use Kiaan\Validator;
use Kiaan\Trans;
use Kiaan\Config;
use Kiaan\Env;
use Kiaan\Csrf;
use Kiaan\Response;
use Kiaan\Collection;
use Kiaan\Password;
use Kiaan\Store;

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
 * Get link
 *
 */
if (!function_exists('link')) {
    function link($url='') {
        return Url::link($url);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Public link
 *
 */
if (!function_exists('publicLink')) {
    function publicLink($path='') {
        return Url::publicLink($path);
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
 * Input
 *
 */
if (!function_exists('input')) {
    function input() {
        return Input::xThis();
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Validate
 *
 */
if (!function_exists('validate')) {
    function validate(array $inputs, array $rules, array $message = []) {
        return Validator::validate($inputs, $rules, $message);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Route
 *
 */
if (!function_exists('route')) {
    function route($name, $parameters=[]) {
       return Route::url($name, $parameters);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Go route
 *
 */
if (!function_exists('goRoute')) {
    function goRoute($name, $parameters=[]) {
        return Route::go($name, $parameters);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Trans
 *
 */
if (!function_exists('_t')) {
    function _t($content, $key, $vars=[], $default=null, $lang=null) {
        return Trans::get($content, $key, $vars, $default, $lang) ;
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Env
 *
 */
if (!function_exists('env')) {
    function env($var) {
        return Env::get($var);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Config
 *
 */
if (!function_exists('config')) {
    function config($content, $key, $default=null) {
        return Config::get($content, $key, $default);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * CSRF
 *
 */
if (!function_exists('csrfToken')) {
    function csrfToken() {
        return Csrf::get();
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Response
 *
 */
if (!function_exists('response')) {
    function response() {
        return Response::xThis();
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Collection
 *
 */
if (!function_exists('collect')) {
    function collect($items) {
        return Collection::collect($items);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Password
 *
 */
if (!function_exists('password')) {
    function password($value) {
        return Password::encrypt($value);
    }
}
#--------------------------------------------------

#--------------------------------------------------
/**
 * Store
 *
 */
if (!function_exists('store')) {
    function store() {
        return Store::xThis();
    }
}
#--------------------------------------------------