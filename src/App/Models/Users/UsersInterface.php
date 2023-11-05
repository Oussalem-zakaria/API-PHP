<?php

namespace App\Models\Users;

interface UsersInterface
{
    public function auth($data, $login);
    public function register($data);
    public function signOut($api_key, $user_id);
    public function createApiKey($user_id);
    public function removeApiKey($api_key, $user_id);
    public function checkIfApiKeyIsValide($api_key, $user_id);
    public function userApiKeyExists($user_id);
}