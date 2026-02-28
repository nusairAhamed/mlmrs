<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestGroup extends Model
{
    protected $fillable = ['category_id', 'name', 'price', 'status'];

    public function category()
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }
}