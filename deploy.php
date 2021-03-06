<?php

namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'my_project');

// User
set('user', function () {
    return runLocally('git config --get user.name');
});

// Project repository
set('repository', 'git@gitlab.com:i2902/any-crawler.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// ENV
set('dotenv', '{{current_path}}/.env');

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);


// Hosts

host('project.com')
    ->set('deploy_path', '~/{{application}}');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

