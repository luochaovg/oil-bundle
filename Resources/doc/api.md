
# 常用的 API

## 对数据进行操作

本框架本质上为 `Symfony` 的一个 `bundle`，所以目录结构都遵循该框架的规范；  
操作数据主要涉及到 `Repository` 和 `Entity` 两个概念，当然你可以通过脚手架直接生成它们；  

* **Entity** 为单纯得数据表结构的映射，里面包含了一些 `Annotation`；  
* **Repository** 和 `Entity` 是一一对应的关系，主要负责对当前数据表进行各式操作，如增删改查。

### 创建 `Repository` 对象

```php
/**
 * @var Repository\UserRepository $userRepo
 */
$userRepo = $this->repo(Entity\User::class);

$userRepo->count(['state' => Abs::NORMAL]);
$userRepo->lister([]);  // 列表方法的参数为数组，较为复杂，请参考方法实现
```

### 创建 `Query builder` 对象

```php
$userQuery = $this->query(Entity\User::class);
```

### 创建 `PDO` 对象

```php
$pdo = $this->pdo();
```

### 获取参数

```php
$this->getArgs('page');                 // $_GET 获取单个
$this->getArgs(['page', 'limit']);      // $_GET 获取多个

$this->postArgs('page');                // $_POST 获取单个
$this->postArgs(['page', 'limit']);     // $_POST 获取多个

$this->allArgs('page');                 // $_GET、$_POST、$_REQUEST 获取单个
$this->allArgs(['page', 'limit']);      // $_GET、$_POST、$_REQUEST 获取多个
```

### 获取客户端 `IP`

```php
$this->getClientIp();
```

### 获取指定 `IP` 定位

```php
$location = $this->ip2regionIPDB('10.1.2.3');
```

### 给当前页面添加资源文件

```php
$this->appendSrcCss('diy:app.css');
$this->appendSrcJs('diy:app.js');
# 或者合并以上两个
$this->currentSrc('diy:app');   // 分别下发 css 和 js
```

### 使用缓存

```php
$this->caching(function() {
    // logic and return data
});
```

### 获取用户信息

```php
dump($this->usr);
```

### 获取配置信息

```php
dump($this->cnf);
```

### 获取 `service.yml` 配置

```php
$this->parameter('key_for_parameter');
```

### 当前请求对象

```php
$this->request();
```

### 生成 `URL`

```php
$this->url('app_route_name');
$this->urlSafe('app_route_name');   // without error when no router
```

### 获取当前项目所有的路由

```php
$this->getRouteCollection();
```

### 实例化组件

```php
$uploader = new Component\Upload();
$rsa = $this->component(Component\Rsa::class);  // 这种方式会根据你预设的配置去实例化组件，即服务定位器
```

### `I18N`

```php
$this->fieldLang('Key in fields.locale.yml');
$this->messageLang('Key in messages.locale.yml');
$this->twigLang('Key in twig.locale.yml');
$this->seoLang('Key in seo.locale.yml');
```

### `Api` 类项目响应请求

```php
$this->okay([]);
$this->success('operate done.');
$this->failed('operate failed.');
```

### `Web` 类和 `Admin` 类项目响应请求

```php
// ajax 类
$this->okayAjax([]);
$this->successAjax('operate done.');
$this->failedAjax('operate failed.');

// ajax 类强化
$this->responseMessage(Bsw\Message $message);
$this->responseMessageWithAjax(Bsw\Message $message);   // 当前方法和上面三个 ajax 方法的区别在于返回前要 flush 一个 Message
$this->responseSuccess('operate done.');                // 调用 responseMessage
$this->responseError('operate failed.');                // 调用 responseMessage

// 页面渲染类
$this->show(['time' => '2o2o/o8/o1'], 'index.html');

// Admin 类项目专用
$this->showBlank([]);           // 渲染一个空页面（带框架，仅主内容块为空）
$this->showPreview([]);         // 渲染数据列表页面
$this->showPersistence([]);     // 渲染持久化表单页面
$this->showChart([]);           // 渲染图表页面
```

### 路由鉴权

```php
$this->routeIsAccess('app_route_name');
```

> 这些只是其中一小部分常用的 `API`，了解更多可直接查阅 `vendor/jtleon/bsw-bundle/Controller/Traits` 目录下的 `Trait`。  
