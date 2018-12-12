<?php
namespace Deployer;

require 'recipe/laravel.php';

set('repository', 'git@github.com:Everan-1994/api.fruit.git');
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

host('39.108.114.179')
    ->user('deployer') // 使用 root 账号登录
    ->identityFile('~/.ssh/deployerkey.pub') // 指定登录密钥文件路径
    ->become('www-data') // 以 www-data 身份执行命令
    ->set('deploy_path', '/data/wwwroot/fruit-api.lzdu.com'); // 指定部署目录

after('deploy:failed', 'deploy:unlock');
before('deploy:symlink', 'artisan:migrate');

