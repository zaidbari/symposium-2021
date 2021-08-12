<?php


namespace App\Models;


class Page extends \Core\Model
{
	/**
	 * @throws \Pixie\Exception
	 */
	public static function all() : array
	{
		$db = static::db();
		return $db->table('pages')->orderBy('name', 'DESC')->get();
	}

	public static function add( $values = []): bool
	{
		$db = static::db();
		return $db->table('pages')->insert($values);
	}

	public static function one( $id): array
	{
		$db = static::db();
		return (array) $db->table('pages')->find($id);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function update( $param, array $data )
	{
		$db = static::db();
		return $db->table('pages')->where('id', $param)->update($data);
	}
}