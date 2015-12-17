<?php
/*
App/Controllers里面放控制器，文件命名格式：ControllernameController.php，类命名格式：ControllernameController。
业务复杂的话，可以分模块，比如增加Movie模块：App/Controllers下创建目录Movie，其下文件文件命名格式：ControllernameController.php，类命名格式：Movie_ControllernameController。
App/Models里面放数据模型，命名格式：ModelnameModel.php，类命名格式：ModelnameModel
业务复杂的话，可以分模块，比如增加Movie模块：App/Models下创建目录Movie，其下文件文件命名格式：ModelnameModel.php，类命名格式：Movie_ModelnameModel。
App/Views里面放模板视图，一个文件夹对应一个controller的视图，文件夹下的视图文件以控制器的方法命名，例如test文件夹对应TestController的视图，
test文件夹下的index.phtml对应TestController的index方法，链接访问方式：http://yourhost/test/index
业务复杂的话，可以分模块，比如增加Movie模块：.....，链接访问方式：http://yourhost/movie/test/index

关于url：
链接访问形式:http://yourhost/mvc/index.php/test/thanks?p1=a&p2=b
其中index.php为入口文件,test为controller,thanks为方法,p1、p2为参数
上面这种方式几乎不需要修改web服务器任何配置。
下面提供另一种链接访问方式：http://yourhost/test/thanks?p1=a&p2=b
这种方式需要修改web服务器配置文件，下面给出nginx.conf的配置
增加
	server {
		listen       80;
		server_name  yourhost;
		root         /var/www/html/mvc;
		# /var/www/html/mvc是你的文件目录，可修改为其他路径
		index  index.php index.html index.htm;
		try_files $uri $uri/ /index.php$is_args$args;
		location ~ \.php$ {
		 root           /var/www/html/mvc;
		 fastcgi_pass   127.0.0.1:9000;                                                                   
		 fastcgi_index  index.php;
		 fastcgi_param  SCRIPT_FILENAME  /var/www/html/mvc$fastcgi_script_name;
		 include        fastcgi_params;
		}
    }

公共底层可以放在Library里，文件命名与类名相同，但是请注意不要与Grug里文件名重复，以避免autoload时出错。

*/


// error_reporting(E_ERROR | E_WARNING | E_PARSE); //设置错误报告
ini_set('display_errors', 'On');
error_reporting(1);
define("BASEDIR", __DIR__);
include BASEDIR."/Grug/Loader.php";
spl_autoload_register("\\Grug\\Loader::autoload");
Application::getInstance(BASEDIR)->dispatch();

?>