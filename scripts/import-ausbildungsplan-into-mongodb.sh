#!/usr/bin/env php
<?php

require realpath(__DIR__ . '/../vendor/autoload.php');

$MONGO_SERVER_IP = getenv('MONGO_SERVER_IP');
if ($MONGO_SERVER_IP === false) {
    $MONGO_SERVER_IP = `docker-machine ip`;
}

$MONGO_SERVER_PORT = '27017';
$MONGO_URI = 'mongodb://' . trim($MONGO_SERVER_IP) . ':' . $MONGO_SERVER_PORT;

$DB = 'reportbook';
$COLLECTION = 'teaching_contents';

$IMPORT_FILE = realpath(__DIR__ . '/../teaching_contents.csv');

if (!file_exists($IMPORT_FILE)) {
    echo "Importfile '$IMPORT_FILE' not found.\n";
    exit(1);
}

$handle = @fopen($IMPORT_FILE, 'r');

if ($handle === false) {
    echo "Could not open '$IMPORT_FILE'.\n";
    exit(1);
}

$client = new MongoDB\Client($MONGO_URI);
$teachingContent = $client->$DB->$COLLECTION;

$teachingContent->drop();

while (($line = fgetcsv($handle)) !== false) {
    $rows = count($line);
    $number = $line[0];
    $profile = $line[1];

    $document = [
        'number' => $number,
        'profile' => $profile,
        'skills' => []
    ];

    for ($i=2; $i < $rows; $i++) {
        if (trim($line[$i]) !== '') {
            $document['skills'][] = $line[$i];
        }
    }

    $insertResult = $teachingContent->insertOne($document);

    printf("%-5s %s (%s)\n"
        , $number
        , $profile
        , $insertResult->getInsertedId()
    );
}

$teachingContent->createIndex(
    [ 'profile' => 'text', 'skills' => 'text' ],
    [ 'default_language' => 'german' ]
);
