<?php


namespace App\Controllers\Admin;


use App\Lib\Upload;
use App\Lib\Validator;
use App\Models\Sponsor;
use Core\View;

class Sponsors extends \Core\Controller
{
	/**
	 * @throws \Pixie\Exception
	 */
	public function index( $request )
	{
		if ( $request->method() == 'POST' ) Sponsor::delete($request->param('sponsor_id'));
		{
			View::render('admin/sponsors/index', [
				'data' => Sponsor::all(),
				'meta' => [
					'title' => 'Sponsors',
					'breadcrumbs' => [
						[ "name" => "Sponsors", "url" => "/admin/sponsors", "active" => true ]
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
		$data = $errors = $products = [];
		$success = $error = $edit = false;

		if ( $request->param('action') == 'edit' ) {
			$edit = true;
			$title = 'Edit sponsor';
			$q = Sponsor::one($request->param('sponsor_id'));
			$data = $q['sponsor'];
			$products = $q['products'];

			$image = $data['logo'];
		} else {
			$title = 'Create sponsor';
		}

		if($request->method() == 'POST' && isset($_POST['delete_product'])) {
			if(Sponsor::deleteProduct($request->param('product_id'))) {
				$success = 'Product Deleted successfully';
				$q = Sponsor::one($request->param('sponsor_id'));
				$data = $q['sponsor'];
				$products = $q['products'];
			}
		} else
		if ( $request->method() == 'POST' ) {
			$data['name'] = trim($request->param('name'));
			$data['description'] = htmlentities(trim($request->param('description')));
			$data['sponsor_type'] = trim($request->param('type'));
			$val = new Validator();
			$rules = [
				'name' => [ 'required', 'minLen' => 2, 'maxLen' => 200 ],
				'description' => [ 'maxLen' => 1000 ],
			];

			$val->validate($request->paramsPost(), $rules);
			if ( $val->error() ) $errors = $val->error();
			else {
				if ( Upload::check('image') ) {
					$file = new Upload('image', 'sponsors');
					if ( $file->move() ) $data['logo'] = $file->save_name;
				} else $data['logo'] = $edit ? $image : null;


				if ( $edit ) {
					if ( Sponsor::update($request->param('sponsor_id'), $data) ) {
						$success = "Sponsor edited successfully.";
					}
				}
				else {

					if ( Sponsor::add($data) ) {
						$success = "Sponsor added successfully";
						$data = [];
					}
				}
			}
		}
		View::render('admin/sponsors/form', [
			'data' => $data,
			'success' => $success,
			'error' => $error,
			'errors' => $errors,
			'products' => $products,
			'edit' => $edit,
			'meta' => [
				'title' => $title,
				'breadcrumbs' => [
					[ "name" => "Sponsors", "url" => "/admin/sponsors", "active" => false ],
					[ "name" => $edit ? 'Edit' : 'Create', "url" => "/sponsors/" . $edit ? 'edit/' . $request->param('sponsor_id') : 'create', "active" => true ]
				]
			],
		]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function manageProduct( $request)
	{
		$data = $errors = [];
		$success = $error = $edit = false;
		if ( $request->param('action') == 'edit' ) {
			$edit = true;
			$title = 'Edit product';
			$data = Sponsor::oneProduct($request->param('product_id'));
			$image = $data['url'];
		} else {
			$title = 'Add product';
		}


		if ( $request->method() == 'POST' ) {
			$data['product_name'] = trim($request->param('name'));
			$data['type'] = trim($request->param('type'));
			$val = new Validator();
			$rules = [
				'name' => [ 'required', 'minLen' => 2, 'maxLen' => 200 ],
				'type' => [ 'required' ]
			];

			$val->validate($request->paramsPost(), $rules);
			if ( $val->error() ) $errors = $val->error();
			else {
				if($request->param('type') == 'video') {
					$data['url'] = $edit ? $image : $request->param('url');
				} else {
					if ( Upload::check('image') ) {
						$file = new Upload('image', 'products');
						if ( $file->move() ) $data['url'] = $file->save_name;
					} else $data['url'] = $edit ? $image : null;
				}


				if ( $edit ) {
					if ( Sponsor::update($request->param('sponsor_id'), $data) ) $success = "Product edited successfully.";
				}
				else {
					if ( Sponsor::addProduct($data, $request->param('sponsor_id')) ) {
						$success = "Product added successfully";
						$data = [];
					}
				}
			}
		}

		View::render('admin/sponsors/product', [
			'data' => $data,
			'success' => $success,
			'error' => $error,
			'errors' => $errors,
			'meta' => [
				'title' => $title,
				'breadcrumbs' => [
					[ "name" => "Sponsors", "url" => "/admin/sponsors", "active" => false ],
					[ "name" => "Products", "url" => "/admin/sponsors/edit/" . $request->param('sponsor_id'), "active" => false ],
					[ "name" => $title, "url" => "/admin/sponsors/" . $request->param('sponsor_id') . "/products/" . $edit ? 'edit': 'create', "active" => true ]
				]
			],
		]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function updatePosition( $request )
	{
		foreach ( $request->param('value') as $key => $value ) {
			$data = $key + 1;
			Sponsor::updatePosition($data, $value);
		}
	}
}