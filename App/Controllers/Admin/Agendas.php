<?php


namespace App\Controllers\Admin;


use App\Lib\Validator;
use App\Models\Agenda;
use App\Models\Speaker;
use Core\View;

class Agendas extends \Core\Controller
{
	/**
	 * @throws \Pixie\Exception
	 */
	public function index($request)
	{
		if($request->method() == "POST") Agenda::delete($request->param('id'));
		$data = Agenda::all();
		$arr = [];
		foreach ($data as $day) $arr[ $day->day_name ][$day->lecture_type][] = $day;

		View::render('admin/agenda/index', [
			'data' => $arr,
			'meta' => [
				'title' => 'Agenda',
				'breadcrumbs' => [
					["name" => "Agenda", "url" => "/agenda", "active" => true]
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

		$speakers = Speaker::agenda();
		$days = Agenda::days();
		$lecture_types = Agenda::lecture_types();


		if( $request->param('action') == 'edit' ) {
			$edit = true;
			$title = 'Edit agenda';
			$data = Agenda::one($request->param('agenda_id'));
		}
		else {
			$title = 'Create agenda';
		}

		if($request->method() == 'POST') {

			$data['start_time'] = strtotime(trim($request->param('start_time')));
			$data['end_time'] = strtotime(trim($request->param('end_time')));
			$data['type'] = trim($request->param('type'));
			$data['talk'] = trim($request->param('lecture'));
			$data['day_id'] = trim($request->param('day_id'));
			$speaker_id = trim($request->param('speaker_id'));

			$val = new Validator();
			$rules = [
				'start_time' => [ 'required' ],
				'end_time' => [ 'required' ],
				'type' => [ 'required' ],
				'talk' => [ 'required' ]
			];

			$val->validate($request->paramsPost(), $rules);
			if ( $val->error() ) $errors = $val->error();
			else {

				if($edit) {
					if( Agenda::update($request->param('agenda_id'), $data)) {
						$success = "Agenda edited successfully.";
					}
				} else {
					if ( Agenda::add($data, $speaker_id) ) {
						$success = "Agenda added successfully";
						$data = [];
					}
				}
			}
		}

		View::render('admin/agenda/form', [
			'data' => $data,
			'days' => $days,
			'lecture_types' => $lecture_types,
			'success' => $success,
			'error' => $error,
			'errors' => $errors,
			'speakers' => $speakers,
			'meta' => [
				'title' => $title,
				'breadcrumbs' => [
					["name" => "Agenda", "url" => "/admin/agenda", "active" => false],
					["name" => $edit ? 'Edit': 'Create', "url" => "/agenda/" . $edit ? 'edit/' . $request->param('agenda_id') : 'create' , "active" => true]
				]
			],
		]);

	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function lecture( $request )
	{
		$data = "";
		 $errors = [];
		$success = $error = $edit =  false;

		if( $request->param('action') == 'edit' ) {
			$edit = true;
			$title = 'Edit lecture';
			$data = Agenda::oneLecture($request->param('lecture_id'))->lecture_type;
		}
		else {
			$title = 'Create lecture';
		}

		if($request->method() == 'POST') {

			$lecture_type = trim($request->param('lecture_type'));

			$val = new Validator();
			$rules = [
				'lecture_type' => [ 'required' ],
			];

			$val->validate($request->paramsPost(), $rules);
			if ( $val->error() ) $errors = $val->error();
			else {

				if($edit) {
					if( Agenda::lectureUpdate($request->param('lecture_id'), $lecture_type)) {
						$success = "Lecture edited successfully.";
					}
				} else {
					if ( Agenda::lectureAdd($lecture_type) ) {
						$success = "Lecture added successfully";
						$data = "";
					}
				}
			}
		}

		View::render('admin/agenda/lecture', [
			'lecture_type' => $data,
			'success' => $success,
			'error' => $error,
			'errors' => $errors,
			'meta' => [
				'title' => $title,
				'breadcrumbs' => [
					["name" => "Agenda", "url" => "/admin/agenda", "active" => false],
					["name" => $edit ? 'Edit': 'Create', "url" => "/agenda/lecture/" . $edit ? 'edit/' . $request->param('lecture_id') : 'create' , "active" => true]
				]
			],
		]);

	}
}