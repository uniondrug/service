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
            │   ├── Exception.php
            │   ├── Registry.php
            │   ├── RequestData.php
            │   ├── RequestReader.php
            │   ├── ResponseData.php
            │   ├── ResponsePaging.php
            │   ├── ResponseWriter.php
            │   └── Types.php
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