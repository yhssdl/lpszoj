<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=ojdate',
    'username' => 'ojdate',
    'password' => '123456',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
