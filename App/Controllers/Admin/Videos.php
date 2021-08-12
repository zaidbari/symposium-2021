<?php


namespace App\Controllers\Admin;


use App\Lib\Validator;
use App\Models\Speaker;
use App\Models\Video;
use Core\View;

class Videos extends \Core\Controller
{
	/**
	 * @throws \Pixie\Exception
	 */
	public function index( $request )
	{
		if($request->method() == 'POST') Video::delete($request->param('video_id'));

		View::render('admin/videos/index', [
			'data' => Video::all(),
			'meta' => [
				'title' => 'All Videos',
				'breadcrumbs' => [
					["name" => "Videos", "url" => "/admin/videos", "active" => true]
				]
			],
		]);

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
			$title = 'Edit video';
			$data = Video::one($request->param('video_id'));
		}
		else {
			$title = 'Add video';
		}

		if($request->method() == 'POST') {
			$data['video_name'] = trim($request->param('name'));
			$data['video_description'] = htmlentities(trim($request->param('description')));
			$data['speaker_id'] = trim($request->param('speaker_id'));
			$data['url'] = trim($request->param('url'));

			$val = new Validator();
			$rules = [
				'name' => [ 'required', 'minLen' => 2, 'maxLen' => 200 ],
				'description' => [ 'maxLen' => 1000 ],
				'speaker_id' => [ 'required' ],
				'url' => [ 'required' ],
			];

			$val->validate($request->paramsPost(), $rules);
			if ( $val->error() ) $errors = $val->error();
			else {

				if($edit) {
					if( Video::update($request->param('video_id'), $data)) {
						$success = "Video edited successfully.";
					}
				} else {
					if ( Video::add($data) ) {
						$success = "Video added successfully";
						$data = [];
					}
				}
			}
		}

		View::render('admin/videos/form', [
			'data' => $data,
			'success' => $success,
			'error' => $error,
			'errors' => $errors,
			'speakers' => Speaker::all(),
			'meta' => [
				'title' => $title,
				'breadcrumbs' => [
					["name" => "Videos", "url" => "/admin/videos", "active" => false],
					["name" => $edit ? 'Edit': 'Add', "url" => "/videos/" . $edit ? 'edit/' . $request->param('video_id') : 'create' , "active" => true]
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
			Video::updatePosition($data, $value);
		}
	}
}