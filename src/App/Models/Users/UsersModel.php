<?php

namespace App\Models\Users;

use App\DataBase\DataBase as DB;
use PDO;
use PDOException;

class UsersModel implements UsersInterface
{
    private $connection;

    public function __construct()
    {
        $db = new DB;
        $this->connection = $db->connect();
    }

    // authantification
    public function auth($data, $login)
    {
        // check if user is exist
        $stm = $this->connection->prepare("SELECT * FROM user_s WHERE email=:email");
        $stm->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $stm->execute();
        $user = $stm->fetch(PDO::FETCH_ASSOC);

        if ($user && $login) {
            $user['apiKey'] = $this->createApiKey($user['id']);
        }

        return $user;
    }

    // SignUp
    public function register($data)
    {
        try {
            $stm = $this->connection->prepare("INSERT INTO user_s (name,email,password) VALUES(:name,:email,:password)");
            $stm->bindParam(":name", $data['username'], PDO::PARAM_STR);
            $stm->bindParam(":email", $data['email'], PDO::PARAM_STR);
            $stm->bindParam(":password", $data['password'], PDO::PARAM_STR);

            $result = $stm->execute();
            return $result;
        } catch (PDOException $e) {
            return $e;
        }
    }
    
    // Logout
    public function signOut($api_key, $user_id)
    {
        $this->removeApiKey($api_key, $user_id);
    }

    public function createApiKey($user_id)
    {
        $apiKey = bin2hex(random_bytes(16));
        $stm = $this->connection->prepare("INSERT INTO api_keys (api_key,user_id) VALUES(:api_key,:user_id)");
        $stm->bindParam(":api_key", $apiKey);
        $stm->bindParam(":user_id", $user_id);
        $stm->execute();

        return $apiKey;
    }

    public function removeApiKey($api_key, $user_id)
    {
        $stm = $this->connection->prepare("DELETE FROM api_keys WHERE api_key=:api_key AND user_id=:user_id");
        $stm->bindParam(":api_key", $api_key);
        $stm->bindParam(":user_id", $user_id);
        $stm->execute();
    }

    public function checkIfApiKeyIsValide($api_key, $user_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM api_keys WHERE api_key=:api_key AND user_id=:user_id");
        $stm->bindParam(":api_key", $api_key);
        $stm->bindParam(":user_id", $user_id);
        $stm->execute();
        $data = $stm->fetch(PDO::FETCH_ASSOC);

        return $data;
    }
}
