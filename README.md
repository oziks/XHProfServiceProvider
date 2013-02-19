XHProfServiceProvider
=====================

Adding XHProf report in the web profiler (Silex).

Parameters
----------

* **xhprof.location** : The path in which XHProf will be found.

* **xhprof.host** : The xhprof host website.

Registering
-----------

``` php
<?php

$app['xhprof.location'] = '/var/www/utils/xhprof';
$app['xhprof.host'] = 'http://localhost/xhprof/';
$app->register(new \Oziks\Provider\XHProfServiceProvider());
```

Usage
-----

``` php
<?php

$app['xhprof']->start();

for ($i = 0; $i <= 1000; $i++) {
    $a = $i * $i;
}

$app['xhprof']->end();
```

Screenshots
-----------

![](https://raw.github.com/oziks/XHProfServiceProvider/master/doc/screenshot_01.png)
