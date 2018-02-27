# uniondrug service

> UnionDrug微服务`MicroService`公共定义。

* PHP `7.1+`
* Phalcon `3.2+`

## 安装

```shell
$ cd project-home
$ composer require uniondrug/service
```

修改 `app.php` 配置文件，加上Cache服务

```php
return [
    'default' => [
        ......
        'providers'           => [
            ......
            \Uniondrug\Service\ServiceServiceProvider::class,
        ],
    ],
];
```


# uniondrug service client

> UnionDrug微服务`MicroService`客户端`consumer`。

### methods

1. Restful请求服务
    1. `delete`(`string`, `string`, `array`, `array`)
    1. `get`(`string`, `string`, `array`)
    1. `head`(`string`, `string`, `array`)
    1. `options`(`string`, `string`, `array`, `array`)
    1. `patch`(`string`, `string`, `array`, `array`)
    1. `post`(`string`, `string`, `array`, `array`)
    1. `put`(`string`, `string`, `array`, `array`)
1. Response结果返回
    1. `withError`(`string`, `int`)
    1. `withList`(`array`)
    1. `withObject`(`array`)
    1. `withPaging`(`array`, `ResponsePaging`)
    1. `withSuccess`()


```php
public function postAction(){
    $name = 'serviceName';
    $route = 'route/action';
    $query = ["page" => 1];
    $body = ["userId" => 1, "options" => ["key" => "value"]];
    $this->serviceClient->post($name, $route, $query, $body);
}
```


# uniondrug service server

> UnionDrug微服务`MicroService`服务端`producer`。

### Methods

1. `withError`(`string`, `int`)
1. `withList`(`array`)
1. `withObject`(`array`)
1. `withPaging`(`array`, `ResponsePaging`)
1. `withSuccess`()
1. `setPaging`(`int`, `int`, `int`)

```php
public function postAction(){
    $total = 123;
    $page = 3;
    $limit = 15;
    $data = [
        ["id" => 1, "key" => "value"],
        ["id" => 2, "key" => "value2"]
    ];
    $this->serviceServer->setPaging($total, $page, $limit)->withPaging($data);
}
```
