<?php

// Session must be started after login
session_start(['save_path' => '/tmp']);

header("Content-Type: text/html");

echo "<h1>GET</h1>\n";
var_dump($_GET);

echo "<h1>POST</h1>\n";
var_dump($_POST);

echo "<h1>SESSION</h1>\n";
var_dump($_SESSION);
