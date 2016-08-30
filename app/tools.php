<?php

function redirect(string $target) {
    header("Location: http://localhost:8000/$target");
}

function session(string $key, $default = null)
{
    if (isset($_SESSION[$key])) {
        return $_SESSION[$key];
    }

    if ($default !== null) {
        return $default;
    }

    return null;
}

function post(string $key, $default = null)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }

    if ($default !== null) {
        return $default;
    }

    return null;
}

function get(string $key, $default = null)
{
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }

    if ($default !== null) {
        return $default;
    }

    return null;
}

function request(string $key, $default = null)
{
    if (isset($_REQUEST[$key])) {
        return $_REQUEST[$key];
    }

    if ($default !== null) {
        return $default;
    }

    return null;
}

function isAuthorized(string $role)
{
    if ((!session('authorized') || session('role') !== $role)) {
        return false;
    }
    return true;
}
