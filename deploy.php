<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'training_deploy_avengers');

// Project repository
set('repository', 'git@github.com:AvengersTraining/ThanhHC-Batch-02-Deploy-PHP-Application-Training.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Default branch
set('branch', 'develop');

// Shared files/dirs between deploys 
add('shared_files', ['.env']);
add('shared_dirs', [
    'storage',
    'bootstrap/cache',
]);

// Writable dirs by web server 
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

// Hosts
host('149.28.136.241')
    ->user('deploy')
    ->stage('production')
    ->set('deploy_path', '~/{{application}}')
    ->forwardAgent(false);
    
// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});
task('yarn:install', function () {
    run('cd {{release_path}} && yarn install');
});
task('yarn:dev', function () {
    run('cd {{release_path}} && yarn dev');
});
task('reload:php-fpm', function () {
    run('sudo /etc/init.d/php7.2-fpm reload');
});

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:optimize',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'reload:php-fpm',
    'yarn:install',
    'yarn:dev',
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
// before('deploy:symlink', 'artisan:migrate');

desc('Deploy done!');
