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
			$data['start_time'] = trim($request->param('start_time'));
			$data['end_time'] = trim($request->param('end_time'));
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
}