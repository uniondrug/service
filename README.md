# 药联微服务协议封装

Uniondrug微服务服务端和客户端的封装。基于HTTP的微服务报文格式形如：

```json
{
    "errno": "0",
    "error": "",
    "dataType": "OBJECT",
    "data": {
        "do2": {
            "from": "do1"
        },
        "do3": {
            "from": "do1"
        }
    }
}
```

通过封装后，在服务端，通过Server的系列方法直接产生上述格式的响应报文。在客户端，通过Client的方法，发起请求，对上述报文自动解析封装，方便使用。

## 安装

```shell
$ cd project-home
$ composer require uniondrug/service
```

修改 `app.php` 配置文件，注入服务。服务名称：服务端 `serviceServer`，客户端 `serviceClient`。

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

## 客户端（Client）

### 使用方法

> 发起请求

方法：

* `delete`(`serviceName`, `route`, `query[]`, `body[]`, `extra[]`)
* `get`(`serviceName`, `route`, `query[]`, `body[]`, `extra[]`)
* `head`(`serviceName`, `route`, `query[]`, `body[]`, `extra[]`)
* `options`(`serviceName`, `route`, `query[]`, `body[]`, `extra[]`)
* `patch`(`serviceName`, `route`, `query[]`, `body[]`, `extra[]`)
* `post`(`serviceName`, `route`, `query[]`, `body[]`, `extra[]`)
* `put`(`serviceName`, `route`, `query[]`, `body[]`, `extra[]`)

说明：

* `serviceName` 服务名称，在注册中心注册的服务名称，发起请求时，会自动从注册中心查找该服务的节点。
* `route` 是服务路由，通常就是应用服务的控制器路由。比如`/member/info`。
* `query[]` 是数组格式的参数，如果有，则以QueryString的方式发送。
* `body[]` 是数组格式的参数，如果有，默认以JSON的格式发送。如果想改变，则使用`extra[]`设定。
* `extra[]` 是数组格式的参数，包括：
--    `type`: `body`的发送方式，模式是`json`，可选的有：`form`: 以form-url-encoded的方式发送；`multipart`：以form-data的方式发送。
--    `timeout`: 发送请求的超时，单位秒
--    `connect_timeout`: 连接节点的超时时间，单位秒
--    `headers`: 数组，可选的头信息。

> 返回值

请求成功则返回一个`Uniondrug\Service\RequestReader`对象：

方法：

* `response()` 获得原始的Response对象。
* `data()` 获得服务返回的结构化结果。也就是`data`里面的内容。
* `headers()` 获得返回结果的头信息。
* `getContents()` 获得返回的Body内容。
* `getDuration()` 获得请求用的时间。
* `hasError()` 返回结果是否有错（应用错误，而非Http请求的错误。Http请求出错，会抛出异常）。
* `getErrno()` 获得错误代码
* `getError()` 获得错误信息


举例：

```php
public function postAction()
{
    $serviceName = 'serviceName';
    $route = '/route/action';
    $query = ["page" => 1];
    $body = ["userId" => 1, "options" => ["key" => "value"]];
    $return = $this->serviceClient->post($serviceName, $route, $query, $body);

    print_r($return->data());
}
```

## 服务端

在服务端的控制器，使用Server封装产生指定格式的响应报文体。

### 使用方法

> 方法

* `Phalcon\Http\Response` - `withError`(`string` **$error**, `int` **$errno** = 1)
* `Phalcon\Http\Response` - `withStruct`(`Uniondrug\Structs\StructInterface` **$struct**)

> 举例

```php
public function postAction()
{
    // codes ignored
    $this->serviceServer->withError("错误原因", 1);
}
```
