<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCategory extends Model
{
    protected $fillable = [
        'name',       
    ];

    public function testGroups()
    {
        return $this->hasMany(TestGroup::class, 'category_id');
    }
}
