<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'title',
        'content',
        'slug',
        'categories_id',
        'status',
    ];

    //accessor image 
    protected function image(): Attribute
    {
        //path image
        $path = 'public/myapigateway/blog/';
        return Attribute::make(
            get: fn ($image) => Storage::disk('do_spaces')->temporaryUrl($path . $image, now()->addMinutes(30))
        );
    }
}
