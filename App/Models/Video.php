<?php


namespace App\Models;


class Video extends \Core\Model
{

	/**
	 * @throws \Pixie\Exception
	 */
	public static function all()
	{
		$db = static::db();
		return $db->table('videos')->leftJoin('speakers', 'id', '=', 'speaker_id')->orderBy('video_position')->get();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function one( $id ) : array
	{
		$db = static::db();


		return (array) $db->query("SELECT videos.*, speakers.* FROM videos LEFT JOIN speakers ON videos.speaker_id = speakers.id WHERE videos.video_id = $id")->first();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function update( $id, array $data )
	{
		$values = [];
		$values['video_name'] = $data['video_name'];
		$values['video_description'] = $data['video_description'];
		$values['speaker_id'] = $data['speaker_id'];
		$values['video_position'] = $data['video_position'];
		$values['url'] = $data['url'];

		$db = static::db();
		return $db->table('videos')->where('video_id', $id)->update($values);
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function delete( $id )
	{
		$db = static::db();
		return $db->table('videos')->where('video_id', $id)->delete();
	}

	/**
	 * @throws \Pixie\Exception
	 */
	public static function add( array $data ) :bool
	{
		$db = static::db();
		$data['video_position'] = $db->table('videos')->count() + 1;
		return $db->table('videos')->insert($data);
	}


	/**
	 * @throws \Pixie\Exception
	 */
	public static function updatePosition( $data, $id )
	{
		$db = static::db();
		return $db->table('videos')->where('video_id', $id)->update(['video_position' => $data]);
	}


}