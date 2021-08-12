<?php namespace App\Models;

use Core\Model;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends Model
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
		return $db->table('users')->where('roles', '<>', 'super')->orderBy('status')->orderBy('created_at', 'DESC')->get();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function byEmail( $value ) : ?\stdClass
	{
		$db = static::db();
		return $db->table('users')->find($value, 'email');
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function byId( $value) : ?\stdClass
	{
		$db = static::db();
		return $db->table('users')->find($value);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function add( $values = []): bool
	{
		$db = static::db();
	if(static::byEmail($values['email']) != null) {
		return false;
	} else
		return $db->table('users')->insert($values);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function changeStatus( $id, $status )
	{
		$db = static::db();
		return $db->table('users')->where('id', $id)->update(['status' => $status]);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function changeRole( $id, $make_admin )
	{
		$db = static::db();
		if($make_admin) $role = 'admin';
		else $role = 'user';

		return $db->table('users')->where('id', $id)->update(['roles' => $role]);
	}
}
