# uniondrug service

> UnionDrug微服务`MicroService`公共定义。

* PHP `7.1+`
* Phalcon `3.2+`


*Directory*

```text
└── vendor
    └── uniondrug
        └── framework
            ├── src
            │   ├── Controllers
            │   │   ├── ServiceClientController.php
            │   │   └── ServiceServerController.php
            │   ├── Providers
            │   │   ├── ConfigProvider.php
            │   │   ├── DatabaseProvider.php
            │   │   ├── LoggerProvider.php
            │   │   └── RouteProvider.php
            │   └── Container.php
            └── README.md
```

*Composer*

```json
{
    "autoload" : {
        "psr-4" : {
            "Pails\\" : "vendor/uniondrug/framework/src"
        }
    }
}
```