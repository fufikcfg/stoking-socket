<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

class UserController extends Controller
{
    public function actionCreate(): int
    {
        $username = $this->prompt('Enter username:', [
            'required' => true,
            'validator' => function($input, &$error) {
                if (User::find()->where(['username' => $input])->exists()) {
                    $error = 'Username already exists!';
                    return false;
                }
                return true;
            }
        ]);

        $token = sprintf('%s%s',
            substr(md5($username), 0, 4),
            \Yii::$app->security->generateRandomString(4)
        );

        $user = new User();
        $user->username = $username;
        $user->access_token = $token;

        if ($user->save()) {
            $this->stdout("User created successfully!\n", BaseConsole::FG_GREEN);
            $this->stdout("Username: {$username}\n");
            $this->stdout("Access Token: {$token}\n");
            return ExitCode::OK;
        }

        $this->stdout("Error creating user:\n", BaseConsole::FG_RED);
        foreach ($user->errors as $errors) {
            foreach ($errors as $error) {
                $this->stdout("- {$error}\n");
            }
        }
        return ExitCode::UNSPECIFIED_ERROR;
    }
}