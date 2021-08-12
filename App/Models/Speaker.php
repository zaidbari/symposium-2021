<?php


namespace App\Models;


class Speaker extends \Core\Model
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
		return $db->table('speakers')->where('published', '=', true)->orderBy('position')->get();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function agenda() : array
	{
		$db = static::db();
		return $db->table('speakers')->orderBy('position')->get();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function add( $values = []): bool
	{
		$db = static::db();
		$values['position'] = $db->table('speakers')->count() + 1;
		return $db->table('speakers')->insert($values);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function one( $id) : array
	{
		$db = static::db();
		return (array) $db->table('speakers')->find($id);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function update( $id, $values )
	{
		$db = static::db();
		return $db->table('speakers')->where('id', $id)->update($values);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function delete( $id)
	{
		$db = static::db();
		return $db->table('speakers')->where('id', $id)->delete();
	}


	/**
	 * @throws \Pixie\Exception
	 */
	public static function updatePosition( $data, $id )
	{
		$db = static::db();
		return $db->table('speakers')->where('id', $id)->update(['position' => $data]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function displayHome( $id, $display = true )
	{
		$db = static::db();
		return $db->table('speakers')->where('id', $id)->update(['display_home' => $display]);

	}
}