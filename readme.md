SktT1Byungi/Session
==============================
PHP Session manager (with non blocking handler)

[![Latest Stable Version](https://poser.pugx.org/skt-t1-byungi/session/v/stable)](https://packagist.org/packages/skt-t1-byungi/session)
[![Total Downloads](https://poser.pugx.org/skt-t1-byungi/session/downloads)](https://packagist.org/packages/skt-t1-byungi/session)
[![License](https://poser.pugx.org/skt-t1-byungi/session/license)](https://packagist.org/packages/skt-t1-byungi/session)

Require
---
PHP 5.6 <= *

Simple Example
---
```php
use SktT1Byungi\Session\Session;

 Session::manager()->start();
 Session::set('aaa', '111');
 
 var_dump(Session::get('aaa') === $_SESSION['aaa']);
 // true
```
Usage
---
## manager()
```php
 Session::manager()->id("id")->name("name")->start();
 // session_id("id");
 // session_id("name");
 // session_start();
 
 Session::manager()->close()->destroy();
 // session_write_close();
 // session_destroy();
 
 Session::manager()->settings([
    'cookie_httponly' => true,
    'use_only_cookies' => true,
 ]);
 // ini_set("session.cookie_httponly", true);
 // ini_set("session.use_only_cookies", true);
 
 Session::manager()->handler(new CustomHandler)->start();
 // used custom handler
```
## helpers
```php
Session::set('aaa', [
    'bbb' => [
        'ccc' => 111,
        'ddd' => 222,
    ],
]);
Session::set('eee', '333');
Session::set('fff', '444');

echo Session::get('aaa.bbb.ccc');
// 111

var_dump(Session::has('ccc'), Session::has('eee'));
// false, true

var_dump(Session::only(['eee', 'fff']));
// ['eee' => '333', 'fff' => '444']

var_dump(Session::except(['aaa']));
// ['eee' => '333', 'fff' => '444']

Session::forget('aaa.bbb'); //or Session::remove('aaa.bbb');
// unset($_SESSION['aaa']['bbb']);
```
detial links : https://laravel.com/docs/5.3/helpers#arrays

## collection
```php
Session::set('aaa', [
    [
        "name" => "bangi",
        "position" => "god",
    ],
    [
        "name" => "faker",
        "position" => "human",
    ],
    [
        "name" => "duke",
        "position" => "human",
    ],
    [
        "name" => "wolf",
        "position" => "pig",
    ],
]);

var_dump(Session::collect('aaa')->where('position', 'human')->all());
// [
//     1 => [
//         "name" => "faker",
//         "position" => "human",
//     ],
//     2 => [
//         "name" => "duke",
//         "position" => "human",
//     ],
// ]
```
detial links :  https://laravel.com/docs/5.3/collections

PSR-7 Middleware (__invoke, Closure)
---
when reaches the middleware point, session start.

## Slim3 Example
```php
$app->add(Session::manager()->handler(new CustomHandler)->id('mySess')->middlware());
```

etc..
---
session blocking(http://konrness.com/php5/how-to-prevent-blocking-php-requests/) 때문에 괜찮은 핸들러 찾아보다가 먼가 조금씩들 아쉬워서 그냥 새로 맹듬... 세션락문제 없고 나중에 핸들러교체 되면서 글로벌세션 변수 사용도 가능하면서 간단하고 또 컴포저로 쓸수 있는걸로...