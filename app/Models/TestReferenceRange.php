<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestReferenceRange extends Model
{
     protected $fillable = [
        'test_id','gender','age_min','age_max','ref_min','ref_max'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
