<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 18 Sep 2019 18:12:41 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Project
 * 
 * @property int $ID
 * @property string $Title
 * @property int $Client_ID
 * 
 * @property \App\Models\Client $client
 * @property \App\Models\Cost $cost
 *
 * @package App\Models
 */
class Project extends Eloquent
{
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'Client_ID' => 'int'
	];

	protected $fillable = [
		'Title',
		'Client_ID'
	];

	public function client()
	{
		return $this->belongsTo(\App\Models\Client::class, 'Client_ID');
	}

	public function cost()
	{
		return $this->hasOne(\App\Models\Cost::class, 'Project_ID');
	}
}
