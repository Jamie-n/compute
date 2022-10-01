
{{--envoy based deploy script, some content of this has been adapted from the GitHub readme: https://github.com/eXolnet/laravel-envoy --}}

@import('exolnet/laravel-envoy')

@task('deploy:build')
    cd "{{ $assetsPath }}"
    npm run build
@endtask

@task('deploy:publish')
cd "{{ $releasePath }}"

php artisan down

php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link

php artisan livewire:discover

php artisan migrate --force

php artisan optimize

php artisan up
@endtask
