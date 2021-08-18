<?php


namespace App\Controllers;


use App\Lib\Upload;
use App\Lib\Validator;
use App\Models\Page;
use App\Models\Poster;
use Core\View;

class PosterSubmission extends \Core\Controller
{

	public function index(  )
	{
		View::render('guest/abstract/index', [
			'data' => Page::one(2)['description'],
			'meta' => ['title' => 'Abstract submission']
		]);
	}


	public function posters(  )
	{
		$data =  Poster::published();

		$abstracts = [];
		if(sizeof($data) > 0)
			foreach ($data as  $abstract) {
				array_push($abstracts, [
					$abstract,
					'authors' => Poster::getAuthors($abstract->id)
				]);
			}

		View::render('guest/abstract/posters', [
			'data' => $abstracts,
			'meta' => ['title' => 'Posters']
		]);
	}


	public function submit($request)
	{
		$data = $errors = [];
		$error = $success = false;

		if($request->method() == 'POST') {
			$data['title'] = $request->param('title');

			if ( Upload::check('image') && Upload::checkType('image', ['jpg', 'jpeg', 'png']) ) {
				$file = new Upload('image', 'posters');
				if ( $file->move() ) {
					$data['image'] = $file->save_name;
				} else {
					$error = true;
					$errors['image'] = ['Poster image is required. There were some errors uploading your poster image. '];
				}
			} else {
				$error = true;
				$errors['image'] = ['File type can only be jpg/jpeg/png. '];
			}

			if ( Upload::check('pdf') && Upload::checkType('pdf',['pdf']) ) {
				$file = new Upload('pdf', 'posters');
				if ( $file->move() ) {
					$data['pdf'] = $file->save_name;
				} else {
					$error = true;
					$errors['pdf'] = ['There were some errors uploading your poster image. '];
				}
			} else {
				$error = true;
				$errors['pdf'] = ['Poster PDF is required. File type can only be pdf. '];
			}

			$val = new Validator();
			$rules = [
				'name' => [ 'required', 'minLen' => 2, 'maxLen' => 200 ],
				'author_title' => ['required', 'maxLen' => 3000 ],
				'affiliation' => ['required', 'maxLen' => 1000 ],
				'email' => [ 'required', 'maxLen' => 1000 ]
			];
			$val->validate($request->param('authors')[0], $rules);
			if ( $val->error() ) {
				$errors['authors'] = ['At least one author is required to submit abstract. All input fields are mandatory'];
				$error = true;
			}


			if(!$error) {

				$abstract_id = Poster::add($data);
				$auths = [];
				$c = 0;
				foreach ($request->param('authors') as $author) {
					array_push($auths, [
						'author_title' => $author['author_title'],
						'name' => $author['name'],
						'email' => $author['email'],
						'affiliation' => $author['affiliation'],
						'abstract_id' => $abstract_id,
						'corres' => $c == 0,
						]);
					$c++;
				}

				if(Poster::addAuthors($auths)) {
					$success = "Poster submitted successfully, You will be notified via email once it gets accepted and published.";
				}

			}
		}

		View::render('guest/abstract/form', [
			'data' => $data,
			'errors' => $errors,
			'success' => $success,
			'meta' => ['title' => 'Submit an eposter abstract']
		]);
	}

}