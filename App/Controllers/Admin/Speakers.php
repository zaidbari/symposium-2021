<?php


namespace App\Controllers\Admin;


use App\Lib\Upload;
use App\Lib\Validator;
use App\Models\Speaker;
use Core\Router;
use Core\View;

class Speakers extends \Core\Controller
{

	/**
	 * @throws \Pixie\Exception
	 */
	public function index( $request )
	{
		if($request->method() == 'POST' && $request->param('delete')) Speaker::delete($request->param('speaker_id') );
		if($request->method() == 'POST' && isset($_POST['display'])) {
			Speaker::displayHome($request->param('speaker_id'), $request->param('display'));
		}
	
		{
			View::render('admin/speakers/index', [
				'data' => Speaker::all(),
				'meta' => [
					'title' => 'Speakers',
					'breadcrumbs' => [
						["name" => "Speakers", "url" => "/admin/speakers", "active" => true]
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
			$title = 'Edit Speaker';
			$data = Speaker::one($request->param('speaker_id'));
			$image = $data['image'];
		}
		else {
			$title = 'Create speaker';
		}

		if($request->method() == 'POST') {
			$data['name'] = trim($request->param('name'));
			$data['bio'] = trim($request->param('bio'));
			$data['affiliation'] = trim($request->param('affiliation'));
			$data['lecture'] = trim($request->param('lecture'));
			$val = new Validator();
			$rules = [
				'name' => [ 'required', 'minLen' => 2, 'maxLen' => 200 ],
				'bio' => [ 'maxLen' => 3000 ],
				'affiliation' => [ 'maxLen' => 1000 ],
				'lecture' => [ 'maxLen' => 1000 ],
			];

			$val->validate($request->paramsPost(), $rules);
			if ( $val->error() ) $errors = $val->error();
			else {
				if ( Upload::check('image') ) {
					$file = new Upload('image', 'speakers');
					if ( $file->move() ) {
						$data['image'] = $file->save_name;
					}
				} else {
					if($edit) {
						$data['image'] = $image;
					} else $data['image'] = null;
				}

				if($edit) {
					if( Speaker::update($request->param('speaker_id'), $data)) {
						$success = "Speaker edited successfully.";
					}
				} else {
					if ( Speaker::add($data) ) {
						$success = "Speaker added successfully";
						$data = [];
					}
				}
			}
		}
		View::render('admin/speakers/form', [
			'data' => $data,
			'success' => $success,
			'error' => $error,
			'errors' => $errors,
			'meta' => [
				'title' => $title,
				'breadcrumbs' => [
					["name" => "Speakers", "url" => "/admin/speakers", "active" => false],
					["name" => $edit ? 'Edit': 'Create', "url" => "/speakers/" . $edit ? 'edit/' . $request->param('speaker_id') : 'create' , "active" => true]
				]
			],
		]);

	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function updatePosition( $request )
	{
		foreach ($request->param('value') as $key => $value) {
			$data = $key + 1;
			Speaker::updatePosition($data, $value);
		}
	}
}