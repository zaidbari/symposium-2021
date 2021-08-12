<?php


namespace App\Controllers\Admin;

use Core\Controller;
use Core\View;

class Dashboard extends Controller
{

	public function index(  )
	{
		View::render('admin/dashboard/index', [
			'meta' => [
				'title' => 'Dashboard',
				'breadcrumbs' => [
					["name" => "Dashboard", "url" => "/dashboard", "active" => true]
				]
			],

		]);
	}
}