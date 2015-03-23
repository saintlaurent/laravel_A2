<?php

class AccountController extends BaseController
{
    public function getCreate()
    {
        if(Auth::id() == '' && Auth::user() == null)
        {
            return View::make('users/create'); 
        }
        $message = 'Please sign out before creating a new account.';
        return View::make('hello')->with('message', $message);

    }
    
    public function postCreate()
    {
        
        $validator = Validator::make(Input::all(),
                array(
                    'email' => 'required|unique:users|max:50|email',
                    'password' => 'required|min:6|max:30',
                    'confirm_password' => 'required|same:password',
                    'captcha' => array('required', 'captcha')
                    )
                );
        
        if($validator->fails())
        {
            // redirect back to create-account with error message
            return Redirect::route('create-account')->withErrors($validator)->withInput();
        }
        else
        {
            $user = new User;
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            $user->activation_token = Input::get('_token');
            $user->activation = false;
            $user->save();
            
            if($user)
            {
                /*
                 * Please go to Mailer.php and replace some contents with 
                 * the actual user name and password of your email account
                 * in order to make email function work.
                 */ 
                require_once('Mailer.php');
                $mailer = new Mailer();
                $mailer->SendMail('account/activation/'.$user->activation_token);
                //return Input::all();
                $message = 'Activation email has been sent. Please confirm and login.';
                return View::make('login', array(
                    'message' => $message
                ));
            }
            else
            {
                return 'Insert failed';
            }
        }
    }
    
    public function initialActivation($token)
    {
        if($token == null)
        {
            if($token == '')
            {
                return View::make('hello');
            }
        }
        // Verify if token is valid
        $user = User::where('activation_token', '=', $token)->where('activation', '=', false)->first();
                //select('SELECT activation_token FROM users WHERE activation_token = \''.$token.'\'');
        if($user)
        {
            // Update activation status
            $user->activation = true;
            $user->save();
            return View::make('login', array('message' => 'Account successfully activated.'));
        }
        else
        {
            return View::make('login', array('message' => 'Account activation failed.'));
        }
    }
    
    public function getSignin()
    {
        if(Auth::id() == '' && Auth::user() == null)
        {
            return View::make('login');
        }
        $message = 'You have already logged in previously. Please log out now and log back in again.';
        return View::make('login', array(
            'message' => $message
        ));
        
    }
    
    public function postSignin()
    {
        // How to implement http://laravel.com/docs/4.2/security
        $auth = Auth::attempt(
                    array(
                        'email' => Input::get('email'), // Check email
                        'password' => Input::get('password'), // Check password
                        'activation' => 1 // Check activation if equals to one
//                        , true // The forth parameter is set to be remembering user
                        
                        /*
                           All parameters above on the left of => must match 
                           column names on database table
                        */
                    )
                );
        
        if($auth)
        {
            $email = Input::get('email');
            $user = User::where('email', '=', $email)->first();
            //return "logged in";
            return View::make('secureTest')->with('user', $user);
        }
        else
        {
            $message = 'Login failed.';
            return View::make('login', array(
                'message' => $message
            ));
        }
    }
    
    public function getSecure()
    {
        if(Auth::id() == '' && Auth::user() == null)
        {
            return Redirect::route('sign-in', array('error' => 'You have not logged in yet'));
        }
        return View::make('/secureTest');
    }
    
    public function getForgotPassword()
    {
        return View::make('forgot_password');
    }
    
    public function postForgotPassword()
    {
        // implement send email to user's email address
        $user = User::where('email', '=', Input::get('email'))->first();
        
        if($user)
        {
            $user->reset_token = Input::get('_token');
            $user->save();
            require_once('Mailer.php');
                $mailer = new Mailer();
                $mailer->SendMail('reset_password/'.$user->reset_token.'/'.$user->id);
        }

        return Redirect::route('sign-in');
    }
    
    public function getResetPassword($token, $id)
    {
        //http://localhost:8000/account/activation/'.$tokenMessage

        // Verify if token is valid
        $user = User::where('reset_token', '=', $token)->where('id', '=', $id)->first();
                //select('SELECT activation_token FROM users WHERE activation_token = \''.$token.'\'');
        if($user)
        {
            return View::make('reset_password');
        }
        else
        {
            return View::make('hello');
        }
    }
    
    public function postResetPassword()
    {
        // implement reset password function
        $user = User::where('email', '=', Input::get('email'))->where('reset_token', '=', Input::get('_token'))->first();
        if($user)
        {
            $user->password = Hash::make(Input::get('password'));
            $user->save();
        }
        return Redirect::route('sign-in');
        //return "got in";
        //return var_dump($user->password."     ".$user->reset_token);
    }
            
}