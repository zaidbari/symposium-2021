<?php


namespace App\Models;


use Core\Model;

class Agenda extends Model
{

	/**
	 * @throws \Pixie\Exception
	 */
	public static function all() : array
	{
		$db = static::db();
		return (array) $db
				->table('agenda')
				->query('SELECT * FROM agenda LEFT JOIN speakers ON speakers.id = agenda.speaker_id LEFT JOIN days ON days.day_id = agenda.day_id LEFT JOIN lecture_type ON lecture_type.lecture_type_id = agenda.type')
				->orderBy('start_time', 'DESC')
				->get();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function days() : array
	{
		$db = static::db();
		return (array) $db->table('days')->get();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function lecture_types() : array
	{
		$db = static::db();
		return (array) $db->table('lecture_type')->get();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function add( $values, $speaker_id): bool
	{
		$db = static::db();
		if(!empty($speaker_id)) {
			$values['speaker_id'] = $speaker_id;
		}

		return $db->table('agenda')->insert($values);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function one( $id): array
	{
		$db = static::db();
		return (array) $db->table('agenda')->find($id, 'agenda_id');
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function delete( $id )
	{
		$db = static::db();

		return $db->table('agenda')->where('agenda_id', $id)->delete();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function update( $param, array $data )
	{
		$db = static::db();
		return $db->table('agenda')->where('agenda_id', $param)->update($data);

	}
}