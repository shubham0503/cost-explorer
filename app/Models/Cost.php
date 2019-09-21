<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 18 Sep 2019 18:12:41 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Cost
 * 
 * @property int $ID
 * @property float $Amount
 * @property int $Cost_Type_ID
 * @property int $Project_ID
 * 
 * @property \App\Models\CostType $cost_type
 * @property \App\Models\Project $project
 *
 * @package App\Models
 */
class Cost extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID' => 'int',
		'Amount' => 'float',
		'Cost_Type_ID' => 'int',
		'Project_ID' => 'int'
	];

	protected $fillable = [
		'ID',
		'Amount',
		'Cost_Type_ID',
		'Project_ID'
	];

	public function cost_type()
	{
		return $this->belongsTo(\App\Models\CostType::class, 'Cost_Type_ID');
	}

	public function project()
	{
		return $this->belongsTo(\App\Models\Project::class, 'Project_ID');
	}
}
