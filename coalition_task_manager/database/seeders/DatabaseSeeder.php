<?php
namespace Database\Seeders;
use App\Models\Project;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder { public function run(): void { foreach (['Website Redesign','Marketing','Internal Operations'] as $name) Project::firstOrCreate(['name'=>$name]); } }
