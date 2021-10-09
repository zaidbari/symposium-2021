<?php


namespace App\Models;


class Sponsor extends \Core\Model
{

	/**
	 * Get all the users as an associative array
	 *
	 * @return array
	 * @throws \Pixie\Exception
	 */
	public static function all() : array
	{
		$db = static::db();
		return $db->table('sponsors')->orderBy('sponsor_type')->orderBy('position')->get();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function add( $values = []): bool
	{
		$db = static::db();
		$values['position'] = $db->table('sponsors')->count() + 1;
		return $db->table('sponsors')->insert($values);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function one( $id ) : array
	{
		$db = static::db();
		return [
			'sponsor' => (array) $db->table('sponsors')->find($id),
			'products' => (array) $db->table('products')->findAll('sponsor_id', $id)
		];
//		SELECT sponsors.*, products.* FROM sponsors LEFT JOIN products ON products.sponsor_id = sponsors.id WHERE sponsors.id = $id

//		SELECT sponsors.*, analytics.counter AS page_views FROM sponsors LEFT JOIN analytics ON analytics.item_id = sponsors.id AND analytics.type = sponsor WHERE sponsors.id = $id
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function update( $id, $values )
	{
		$db = static::db();
		return $db->table('sponsors')->where('id', $id)->update($values);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function delete( $id)
	{
		$db = static::db();
		return $db->table('sponsors')->where('id', $id)->delete();
	}


	/**
	 * @throws \Pixie\Exception
	 */
	public static function updatePosition( $data, $id )
	{
		$db = static::db();
		return $db->table('sponsors')->where('id', $id)->update(['position' => $data]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function addProduct( $data, $id) : bool
	{
		$db = static::db();
		$data['sponsor_id'] = $id;
		return $db->table('products')->insert($data);

	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function deleteProduct( $id )
	{
		$db = static::db();
		return $db->table('products')->where('product_id', $id)->delete();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function oneProduct( $id ) : array
	{
		$db = static::db();
		return (array) $db->table('products')->where('product_id', $id)->first();

	}
}