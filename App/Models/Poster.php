<?php


namespace App\Models;


class Poster extends \Core\Model
{
	public static function add( $data )
	{
		$db = static::db();
		$data['position'] = $db->table('abstracts')->count() + 1;

		return $db->table('abstracts')->insert($data);
	}

	public static function all()
	{
		$db = static::db();
		return (array) $db->table('abstracts')->get();
	}

	public static function published()
	{
		$db = static::db();
		return (array) $db->table('abstracts')->where('published', '=', true)->get();
	}

	public static function addAuthors( $data )
	{
		$db = static::db();
		return $db->table('abstract_authors')->insert($data);

	}

	public static function getAuthors( $id )
	{
		$db = static::db();
		return (array) $db->table('abstract_authors')->where('abstract_id', '=', $id)->get();
	}

	public static function publish( $param, array $array )
	{
		$db = static::db();
		return $db->table('abstracts')->where( 'id', '=', $param)->update($array);
	}
}