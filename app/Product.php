<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'slug', 'price','description'];
    protected $hidden = ['slug'];

    public function categories()
    {
        return $this->belongsToMany('App\Category','product_categories');
    }
}
