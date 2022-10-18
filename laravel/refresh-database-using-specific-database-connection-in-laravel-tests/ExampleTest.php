<?php
namespace Tests\Feature;

use Tests\RefreshesDatabase;

class ExampleTest
{
    use RefreshesDatabase;
    
    public string $connectionToMigrate = 'mysql-elevated';
}