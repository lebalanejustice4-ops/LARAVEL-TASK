# TaskFlow — Laravel Task Manager

Starter project for the Coalition Technologies practical task. Requires PHP 8.3+, Laravel 11+, Composer and MySQL.

## Features
- Create, edit and delete tasks
- Drag-and-drop reordering with saved priorities
- MySQL persistence and Laravel timestamps
- Project dropdown filters tasks by project

## Setup
1. Create a fresh Laravel 11 project: `composer create-project laravel/laravel task-manager "^11.0"`
2. Copy these files into the corresponding paths in the Laravel project.
3. Configure `.env` with your MySQL database credentials (`DB_CONNECTION=mysql`, database name, username and password).
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Run `php artisan serve` and open the local URL shown by Artisan.

This ZIP contains application files, not the Laravel framework or `vendor/` dependencies. Test locally and fix any environment-specific issues before submitting. Do not claim it is deployed or tested unless you have verified it yourself.
