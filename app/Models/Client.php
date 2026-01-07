<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'national_id_or_cr',
        'phone',
        'email',
        'city',
        'district',
        'address',
        'status',
        'notes_internal',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * المشاريع المرتبطة بالعميل
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    /**
     * المرفقات
     */
    public function attachments()
    {
        return $this->hasMany(ClientAttachment::class)->latest();
    }

    /**
     * الملاحظات
     */
    public function notes()
    {
        return $this->hasMany(ClientNote::class)->latest();
    }

    /**
     * الحصول على نوع العميل بالعربية
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'individual' => 'فرد',
            'company' => 'شركة',
            'government' => 'جهة حكومية',
            default => $this->type,
        };
    }

    /**
     * الحصول على حالة العميل بالعربية
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            default => $this->status,
        };
    }
}
