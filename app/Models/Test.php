<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = ['name','unit','data_type','sort_order','status'];

    public function ranges()
    {
        //return $this->hasMany(TestReferenceRange::class);
        return $this->hasMany(\App\Models\TestReferenceRange::class, 'test_id');
    }
}
