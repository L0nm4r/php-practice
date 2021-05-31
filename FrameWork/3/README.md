## PHP 框架基础

### Composer实现自动加载
创建配置文件composer.json
```json
{
    "autoload": {
        "psr-4": {
            "app\\":"application/"
        }
    }
}
```
当需要加载`app`命名空间里的类的时候,将`app`命名空间中的子命名空间映射为application目录下的子目录

如 `app\index\controller\Index`=>`application/index/controller/Index.php`

然后执行`composer install` 会自动生成vendor目录 

在入口文件处引入`../vendor/autoload.php`即可实现自动加载

### composer加载tp依赖/组件
安装thinkphp助手函数依赖: `composer require topthink/think-helper=~1.0`

composer.json变成下面这样
```json
{
    "autoload": {
        "psr-4": {
            "app\\":"application/"
        }
    },
    "require": {
        "topthink/think-helper": "~1.0"
    }
}
```
在`\vendor\topthink\think-helper\composer.json`可以看到这个包的命名空间
```json
"autoload": {
    "psr-4": {
        "think\\helper\\": "src"
    },
    "files": [
        "src/helper.php"
    ]
}
```

### 控制反转与依赖注入
参考: https://zhuanlan.zhihu.com/p/33492169
```php
namespace
```