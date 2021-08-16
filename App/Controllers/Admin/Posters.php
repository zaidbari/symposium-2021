<?php


namespace App\Controllers\Admin;


use App\Models\Poster;
use Core\View;

class Posters extends \Core\Controller
{

	public function index( $request )
	{


		if($request->method() == 'POST' && isset($_POST['publish'])) Poster::publish($request->param('abstract_id'), [ 'published' => $request->param('publish')]);

		$data =  Poster::all();

		$abstracts = [];
		if(sizeof($data) > 0)
		foreach ($data as  $abstract) {
			array_push($abstracts, [
				 $abstract,
				'authors' => Poster::getAuthors($abstract->id)
			]);
		}

		View::render('admin/abstracts/index', [
			'data' => $abstracts,
			'meta' => [
				'title' => 'Abstracts',
				'breadcrumbs' => [
					["name" => "Abstracts", "url" => "/admin/abstracts", "active" => true]
				]
			],
		]);
	}

}