<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $fillable = [
        'company_name',
        'logo',
        'image',
        'thumbnail_image',
        'location',
        'mobile_number',
        'phone_number',
        'email',
        'website',
        'staff_mobile_number',
        'staff_email',
        'staff_photo',
        'staff_title',
        'description_1',
        'description_2',
    ];
}