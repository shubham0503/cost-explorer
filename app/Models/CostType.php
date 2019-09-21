<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 18 Sep 2019 18:12:41 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CostType
 * 
 * @property int $ID
 * @property string $Name
 * @property int $Parent_Cost_Type_ID
 * 
 * @property \App\Models\CostType $cost_type
 * @property \Illuminate\Database\Eloquent\Collection $cost_types
 * @property \App\Models\Cost $cost
 *
 * @package App\Models
 */
class CostType extends Eloquent
{
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'Parent_Cost_Type_ID' => 'int'
	];

	protected $fillable = [
		'Name',
		'Parent_Cost_Type_ID'
	];

	public function cost_type()
	{
		return $this->belongsTo(\App\Models\CostType::class, 'Parent_Cost_Type_ID');
	}

	public function cost_types()
	{
		return $this->hasMany(\App\Models\CostType::class, 'Parent_Cost_Type_ID');
	}

	public function cost()
	{
		return $this->hasOne(\App\Models\Cost::class, 'Cost_Type_ID');
	}
}
