<?php
return array(
	//'配置项'=>'配置值'
	'DEFAULT_FILTER'   =>  'strip_tags',
	//数据库配置信息
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => '127.0.0.1', // 服务器地址
	'DB_NAME'   => 'dgche', // 数据库名
	'DB_USER'   => 'root', // 用户名
	//'DB_PWD'    => 'root', // 密码
	'DB_PWD'    => '', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'syd_', // 数据库表前缀 
	'DB_CHARSET'=> 'utf8', // 字符集
	'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
	'MODULE_ALLOW_LIST'    =>    array('Super','Mobile', 'Pc'),
	'DEFAULT_MODULE'       =>    'Mobile',
	'URL_MODULE_MAP'    =>    array('super'=>'admin'),  //模块映射
	//'URL_MODEL'=>1,
	'URL_MODEL'=>3,


);
