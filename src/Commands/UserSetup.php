<?php

namespace alzen8work\UserAssistant\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

const PASSWORD_LENGTH = 32;

class UserSetup extends Command
{
    protected $signature = 'user:setup';

    protected $description = 'Add New User to your project';

    private $user;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $return_val = $this->getDetails();
        $return_val['password'] = $this->generatePassword();
        $return_val['status'] = $this->creatingNewUser($return_val);
        $this->display($return_val);
    }

    private function getDetails()
    {
        $return_val = [];
        $return_val['name'] = $this->askForUsername();
        $return_val['email'] = $this->askForEmail();

        if (!empty($return_val['name']) && !empty($return_val['email'])) {
            $this->info(print_r($return_val));
            $question = 'Are you sure to create user with the detail above?';
            $options = ['Yes', 'No'];
            $selectedChoice = $this->choice($question, $options, $options[0]);
            if ($selectedChoice != $options[0]) {
                $this->getDetails();
            }
        }
        return $return_val;
    }

    private function creatingNewUser($user)
    {
        $return_val = [];

        $info = "Creating User...";
        $this->info($info);

        $result = DB::table('users')->insert([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => bcrypt($user['password']),
        ]);

        if (!empty($result)) {
            $return_val['result'] = $result;
            $return_val['message'] = 'User successfully created!';
        } else {
            $return_val['result'] = 0;
            $return_val['message'] = 'Oops, something went wrong...';
        }

        return $return_val;
    }

    private function generatePassword()
    {
        $return_val = [];
        $info = "Generating Password...";
        $this->info($info);
        $return_val['password'] = $this->randomPassword(PASSWORD_LENGTH);
        if (empty($return_val['password'])) {
            $question = 'Password is not being generated, try again?';
            $options = ['Yes', 'No'];
            $selectedChoice = $this->choice($question, $options, $options[0]);
            if ($selectedChoice == $options[0]) {
                $this->generatePassword();
            } else {
                $err = "Oops something went wrong...";
                $this->error($err);
                die();
            }
        }
        return $return_val['password'];
    }

    private function askForUsername()
    {
        $return_val = [];
        while (empty($return_val['name'])) {
            $name = $this->ask('Name of this user');
            if (empty($name)) {
                $err = "Invalid format, please try again...";
                $this->error($err);
            } else {
                $return_val['name'] = $name;
            }
        }
        return $return_val['name'];
    }

    private function askForEmail()
    {
        $return_val = [];
        while (empty($return_val['email'])) {
            $email = $this->ask('Email of this user');
            $err = '';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $err = "Invalid format, please try again...";
            }
            if (!empty(DB::table('users')->where('email', $email)->exists())) {
                $err = "Email has already being used, please try another email...";
            }

            if (!empty($err)) {
                $this->error($err);
            } else {
                $return_val['email'] = $email;
            }
        }
        return $return_val['email'];
    }
    private function display($info)
    {
        $status = $info['status'];
        unset($info['status']);
        if (empty($status['result'])) {
            $this->error($status['message']);
        } else {
            $this->info($status['message']);
            $this->info('the user info are as below:');
            $this->info(print_r($info));

            // $headers = ['Key', 'Value'];
            // $this->table($headers, $info);
        }
    }

    private function randomPassword($len = 8)
    {
        //enforce min length 8
        if ($len < 8) {
            $len = 8;
        }

        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets = array();
        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = '23456789';
        $sets[] = '~!@#$%^&*(){}[],./?';

        $password = '';

        //append a character from each set - gets first 4 characters
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }

        //use all characters to fill up to $len
        while (strlen($password) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];

            //add a random char from the random set
            $password .= $randomSet[array_rand(str_split($randomSet))];
        }

        //shuffle the password string before returning!
        return str_shuffle($password);
    }
}
