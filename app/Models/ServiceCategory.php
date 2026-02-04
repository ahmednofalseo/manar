<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * الخدمات التابعة للفئة
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /**
     * الخدمات النشطة فقط
     */
    public function activeServices()
    {
        return $this->services()->where('is_active', true);
    }
}
