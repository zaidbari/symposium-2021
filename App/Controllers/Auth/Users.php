<?php

namespace App\Controllers\Auth;

use App\Lib\Validator;
use App\Models\User;
use Core\Controller;
use Core\View;

class Users extends Controller
{

	/**
	 * @throws \Pixie\Exception
	 */
	public function login( $request )
	{
		self::check();
		$success = false;
		$errors = $data = [];
		if ($request->method() == 'POST') {
			$email = $data['email'] = trim($request->param('email'));
			$password = $data['password'] = trim($request->param('password'));

			$val = new Validator();
			$rules = [
				'email' => [ 'required', 'minLen' => 6, 'maxLen' => 150, 'email' ],
				'password' => [ 'required' ]
			];

			$val->validate($request->paramsPost(), $rules);
			if ( $val->error() )
				$errors = $val->error();
			else {
				$user = User::byEmail($email);
				if ( $user ) {
					if ( password_verify($password, trim($user->password)) ) {
						$data = $user;
						$_SESSION['USER'] = $data;
						$_SESSION['LAST_ACTIVITY'] = time();
						header('Location: /dashboard');
						exit();
					} else {
						$errors['password'][0] = 'Incorrect password';
					}
				} else {
					$errors['email'][0] = 'Not Registered';
				}
			}
		}
		View::render('auth/login/index', [ 'meta' => [ 'title' => 'Login' ], 'data' => $data, 'success' => $success, 'errors' => $errors ]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
//	public function register( $request )
//	{
//		self::check();
//
//		$success = $error = false;
//		$errors = $data = [];
//		if($request->method() == 'POST') {
//			$data['name'] = trim($request->param('name'));
//			$data['email'] = trim($request->param('email'));
//			$password = $data['password'] = trim($request->param('password'));
//			$confirm_password = trim($request->param('confirm_password'));
//
//			$val = new Validator();
//			$rules = [
//				'name' => [ 'required', 'minLen' => 2, 'maxLen' => 200 ],
//				'email' => [ 'required', 'minLen' => 6, 'maxLen' => 150, 'email' ],
//				'password' => [ 'required', 'minLen' => 8, 'maxLen' => 64 ],
//				'confirm_password' => ['required', 'minLen' => 8, 'maxLen' => 64]
//			];
//
//
//			if ( empty($confirm_password) ) {
//				$errors['confirm_password'] = ["Confirm password field is required"];
//			} else if ( $confirm_password !== $password ) {
//				$errors['confirm_password'] = ["Passwords do not match"];
//			}
//
//			$val->validate($request->paramsPost(), $rules);
//			if ( $val->error() )
//				$errors = $val->error();
//			else {
//				$data['password'] = password_hash(trim($request->param('password')), PASSWORD_DEFAULT);
//				if ( User::add($data) ) {
//					$success = "You have been registered successfully";
//					$data = [];
//				} else {
//					$error = "Email already exists";
//				}
//			}
//		}
//
//		View::render('auth/register/index', [ 'meta' => [ 'title' => 'Register' ], 'data' => $data, 'error' => $error, 'success' => $success, 'errors' => $errors ]);
//	}

	public function register(  )
	{
		View::render('auth/register/index', [ 'meta' => [ 'title' => 'Register' ] ]);
	}

	public function logout()
	{
		session_unset();     // unset $_SESSION variable for the run-time
		session_destroy();   // destroy session data in storage
		header('Location: /login');
		exit();
	}

	public static function check()
	{
		if ( isset($_SESSION['USER']) ) {
			header('Location: /dashboard');
			exit();
		}
	}

	public function unauthorized()
	{
		View::render('auth/unauthorized/index', [ 'meta' => [ 'title' => 'Unauthorized','breadcrumbs' => [
			["name" => "Unauthorized", "url" => "/unauthorized", "active" => true]
		] ] ]);
	}

	public function approval()
	{
		if(!isset($_SESSION['USER'])) {
			header('Location: /login');
			exit();
		}
		View::render('auth/approval/index', [ 'meta' => [ 'title' => 'Approval','breadcrumbs' => [
			["name" => "Approval", "url" => "/approval", "active" => true]
		] ] ]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function manage($request)
	{
		if($request->method() == 'POST' && isset($_POST['status'])) User::changeStatus($request->param('id'), $request->param('status'));
		if($request->method() == 'POST' && isset($_POST['make_admin'])) User::changeRole($request->param('id'), $request->param('make_admin'));

		View::render('admin/users/index', [
			'meta' => [
				'title' => 'Users',
				'breadcrumbs' => [
					[
						"name" => "Users",
						"url" => "/users",
						"active" => true
					]
				]
			],
			'data' => User::all()
		]);
	}
}