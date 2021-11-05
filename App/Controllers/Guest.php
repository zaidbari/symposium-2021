<?php namespace App\Controllers;

use App\Models\Agenda;
use App\Models\Analytic;
use App\Models\Page;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\Video;
use \Core\View;
use \Core\Controller;

class Guest extends Controller
{

	/**
	 * Show the index page
	 *
	 *
	 * @return void
	 * @throws \Pixie\Exception
	 */
	public function index()
	{
		$data = Agenda::all();
		$speakers = Speaker::all();
		$arr = [];
		foreach ($data as $day) $arr[ $day->day_name ][$day->lecture_type][] = $day;


		View::render('guest/home/index',
			[
				'sponsors' => Sponsor::all(),
				'data' => $arr,
				'speakers' => $speakers,
				'meta' => [ 'title' => 'Home' ]
			]);

	}

	/**
	 * Show the speakers page
	 *
	 * @return void
	 * @throws \Pixie\Exception
	 */
	public function speakers()
	{

		View::render('guest/speakers/index',
			[
				'data' => Speaker::all(),
				'meta' => [ 'title' => 'Speakers' ]
			]);

	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function sponsors()
	{
		View::render('guest/sponsors/index',
		[
			'data' => Sponsor::all(),
			'meta' => ['title' => 'Sponsors']
		]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function sponsorSingle($request)
	{
		$data = Sponsor::one($request->param('sponsor_id'));
		$sponsor = $data['sponsor'];
		$products = $data['products'];

		Analytic::add('sponsor', $request->param('sponsor_id'), $sponsor['sponsor_clicks']);

		View::render('guest/sponsors/single',
			[
				'data' => $sponsor,
				'products' => $products,
				'meta' => ['title' => $sponsor['name']]
			]);
	}

	public function contact(  )
	{
		View::render('guest/contact/index', [
			'data' => Page::one(1)['description'],
			'meta' => ['title' => 'Contact us']
		]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function videos(  )
	{
		View::render('guest/videos/index', [
			'data' => Video::all(),
			'meta' => [
				'title' => 'Videos',

			],
		]);

	}

	public function venue()
	{
		View::render('guest/venue/index', [
			'data' => Page::one(3)['description'],
			'meta' => ['title' => 'Venue']
		]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public function productAnalytic( $request ) : string
	{
		$data = json_decode($request->body());
		$product = Sponsor::oneProduct($data->id);
		Analytic::add($data->type, $data->id, $product['product_clicks']);
		return json_encode(['Success' => true]);
	}

}
