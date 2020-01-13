#Lumen Vue Framework

##环境要求：

* PHP 7.1以上
* Nodejs 8以上
* Apache 2
* Mysql 5.6
* Composer
* Redis （如果用Redis作为缓存）

Install steps:

在项目根目录下运行``composer install -v``

1. 设置 `storage` 目录必须让服务器有写入权限(chmod -R 777 storage)。
2. 设置Apache或Nginx的host目录为项目的**public**目录下
1. ``cp .env.example .env`` .env是Lumen的配置文件，包含了前端缓存配置，调试模式开启与关闭，缓存类配置与设置，具体配置详见.env配置
2. 运行 `` npm install ``

##前端编译调试
       
编译调试前端：`gulp`
编译压缩后的production js文件：`npm run build`

##目录结构

* app目录
  * AcuvueFoundation 访问后台api类和各种定义的接口
  * Http 主要的访问api控制器
  * IndexController.php为主页控制器
  * Controllers/Api/V1 所有的API访问文件
  * Middleware 为中间件，控制接口访问权限
  
* bootstrap 为启动目录，加载各种类与配置文件
* config为配置目录，有关微信，缓存，JWT配置
* public目录为php入口index.php文件位置，各种js资源文件与图片文件
* resources为所有Vue文件与Webpack配置文件位置，php模板文件在views里
* routes为php路由配置，api路由配置在api/v1.php文件中
* storage为临时文件夹，logs为运行日志文件
