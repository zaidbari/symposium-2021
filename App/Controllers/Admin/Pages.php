<?php


namespace App\Controllers\Admin;


use App\Lib\Validator;
use App\Models\Page;
use Core\View;

class Pages extends \Core\Controller
{

	/**
	 * @throws \Pixie\Exception
	 */
	public function index( )
	{
		{
			View::render('admin/pages/index', [
				'data' => Page::all(),
				'meta' => [
					'title' => 'Pages',
					'breadcrumbs' => [
						["name" => "Pages", "url" => "/admin/pages", "active" => true]
					]
				],
			]);
		}
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function manage( $request )
	{
		$data = $errors = [];
		$success = $error = $edit =  false;

		if( $request->param('action') == 'edit' ) {
			$edit = true;
			$title = 'Edit page';
			$data = Page::one($request->param('page_id'));
		}
		else {
			$title = 'Create page';
		}

		if($request->method() == 'POST') {
			$data['name'] = trim($request->param('name'));
			$data['description'] = htmlentities(trim($request->param('description')));

			$val = new Validator();
			$rules = [
				'name' => [ 'required', 'minLen' => 2, 'maxLen' => 200 ],
				'description' => [ 'maxLen' => 5000 ],
			];

			$val->validate($request->paramsPost(), $rules);
			if ( $val->error() ) $errors = $val->error();
			else {

				if($edit) {
					if( Page::update($request->param('page_id'), $data)) {
						$success = "Page edited successfully.";
					}
				} else {
					if ( Page::add($data) ) {
						$success = "Page added successfully";
						$data = [];
					}
				}
			}
		}

		View::render('admin/pages/form', [
			'data' => $data,
			'success' => $success,
			'error' => $error,
			'errors' => $errors,
			'meta' => [
				'title' => $title,
				'breadcrumbs' => [
					["name" => "Pages", "url" => "/admin/pages", "active" => false],
					["name" => $edit ? 'Edit': 'Create', "url" => "/pages/" . $edit ? 'edit/' . $request->param('page_id') : 'create' , "active" => true]
				]
			],
		]);

	}

}