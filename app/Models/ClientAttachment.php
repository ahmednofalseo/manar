<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'category',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * العميل
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * من رفع الملف
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * الحصول على مسار الملف الكامل
     */
    public function getFullPathAttribute()
    {
        return storage_path('app/public/' . $this->file_path);
    }

    /**
     * الحصول على رابط الملف
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
