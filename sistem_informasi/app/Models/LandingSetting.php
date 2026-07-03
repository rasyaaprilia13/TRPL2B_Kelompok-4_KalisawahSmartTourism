<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $table = 'landing_settings';

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_image_path',
        'hero_image_path_2',
        'hero_image_path_3',
    ];
}
