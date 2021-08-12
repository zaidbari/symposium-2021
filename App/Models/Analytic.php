<?php


namespace App\Models;


use App\Controllers\Admin\Analytics;

class Analytic extends \Core\Model
{
	/**
	 * @throws \Pixie\Exception
	 */
	public static function add( $type, $id, $clicks )
	{
		$db = static::db();

		
		$data = $db->table('analytics')
			->where('item_id', $id)
			->where('type', $type)
			->where('ip', Analytics::get_ip())
			->first();


		if(is_null($data)) {
			if($type == 'sponsor') {
				$db->table('sponsors')->where('id', $id)->update(['sponsor_clicks' => $clicks + 1]);
			}
			else
			$db->table('products')->where('product_id', $id)->update(['product_clicks' => $clicks + 1]);
			return $db->table('analytics')->insert([
				'ip' => Analytics::get_ip(),
				'type' => $type,
				'item_id' => $id
			]);
		} else {
			$counter = $data->counter + 1;
			return $db->table('analytics')->where('id', $data->id)->update([
				'counter' => $counter
			]);

		}


//		return $db->table('analytics')->updateOrInsert('');
	}

}