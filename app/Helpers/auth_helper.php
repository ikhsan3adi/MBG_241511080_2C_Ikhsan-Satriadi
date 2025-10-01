<?php

function isAuthenticated(): bool
{
    return currentUser() !== null && is_array(currentUser()) && isset(currentUser()['email']);
}

function setCurrentUser(?array $user): void
{
    \Config\Auth::$currentUser = $user;
}

function currentUser(): ?array
{
    return \Config\Auth::$currentUser;
}

function jwtSecretKey(): string
{
    return env('authjwt.secret', 'default_secret_key_change_me');
}
