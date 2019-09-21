<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 18 Sep 2019 18:12:41 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Client
 * 
 * @property int $ID
 * @property string $Name
 * 
 * @property \Illuminate\Database\Eloquent\Collection $projects
 *
 * @package App\Models
 */
class Client extends Eloquent
{
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $fillable = [
		'Name'
	];

	public function projects()
	{
		return $this->hasMany(\App\Models\Project::class, 'Client_ID');
	}
}
