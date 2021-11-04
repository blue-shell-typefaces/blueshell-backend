<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'blueshell-backend');

// Project repository
set('repository', 'git@github.com:blue-shell-typefaces/blueshell-backend.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);
set('writable_mode', 'chmod');
set('http_user', getenv('DEPLOY_HTTP_USER'));

// Hosts
host(getenv('DEPLOY_HOST'))
    ->setRemoteUser(getenv('DEPLOY_USER'))
    ->setPort(getenv('DEPLOY_PORT'))
    ->setDeployPath(getenv('DEPLOY_PATH'));   
    
// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

