<?php

function getDBConnection(): PDO {

    $host = 'aws-1-us-east-1.pooler.supabase.com';
    $port = '5432';
    $name = 'postgres';
    $user = 'postgres.xknoblstxzvlnjavqgjh';
    $pass = 'dbclientes20##';

    $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";

    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
}