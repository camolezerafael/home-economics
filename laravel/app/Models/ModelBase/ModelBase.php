<?php

namespace App\Models\ModelBase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelBase extends Model{

	use HasFactory;

	public function labels():array
	{
		return [];
	}
}
