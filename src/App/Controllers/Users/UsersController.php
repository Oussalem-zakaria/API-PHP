<?php

namespace App\Controllers\Users;

use App\Models\Users\UsersInterface;

class UsersController
{
    private $user;

    public function __construct(UsersInterface $usersInterface)
    {
        $this->user = $usersInterface;
    }

    public function login($data)
    {
        if (!empty(!empty($data["email"])) && !empty($data["password"])) {
            $user = $this->user->auth($data, $login = true);
            if ($user) {
                // check that he is enter a valid password
                if (password_verify($data['password'], $user['password'])) {
                    unset($user['password']);
                    // loign
                    echo json_encode([
                        'user' => $user,
                        'message' => "Login success"
                    ]);
                    // logout
                } else {
                    echo json_encode([
                        'error' => true,
                        'message' => "invalid password"
                    ]);
                }
            } else {
                echo json_encode([
                    'error' => true,
                    'message' => "User dosn't exist, Try to register"
                ]);
            }
        } else {
            echo json_encode([
                'error' => true,
                'message' => "All fileds are important"
            ]);
        }
    }

    public function signUp($data)
    {
        if (!empty($data['username']) && !empty($data["email"]) && !empty($data["password"])) {
            // check if the user is exist alredy in db
            $result = $this->user->auth($data, $login = false);

            if ($result) {
                echo json_encode([
                    'error' => true,
                    'message' => "User Alredy exist, Try to login"
                ]);
            } else {
                $options = [
                    'const' => 12,
                ];
                $password = password_hash($data["password"], PASSWORD_BCRYPT, $options);
                $data['password'] = $password;
                $result = $this->user->register($data);
                if ($result) {
                    echo json_encode([
                        'message' => "Register success"
                    ]);
                } else {
                    echo json_encode([
                        'error' => true,
                        'message' => "Register failed"
                    ]);
                }
            }
        } else {
            echo json_encode([
                'error' => true,
                'message' => "All fileds are important"
            ]);
        }
    }

    public function logout($data)
    {
        if (!$data['api_key'] || empty($data['api_key'])) {
            http_response_code(404);
            echo json_encode([
                'error' => true,
                'message' => "apiKey not valide"
            ]);
        } else if (!$this->user->checkIfApiKeyIsValide($data['api_key'], $data['user_id'])) {
            http_response_code(404);
            echo json_encode([
                'error' => true,
                'message' => "Api not valide"
            ]);
        } else {
            $this->user->signOut($data['api_key'], $data['user_id']);
            echo json_encode([
                'message' => "Logout success"
            ]);
        }
    }
}
