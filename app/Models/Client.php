<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
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
     * الاسم المعروض حسب لغة الواجهة.
     */
    protected function displayName(): Attribute
    {
        return Attribute::get(function () {
            if (app()->getLocale() === 'en' && filled($this->name_en)) {
                return $this->name_en;
            }

            return $this->name ?? '';
        });
    }

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
     * المستندات المرتبطة
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * الملاحظات
     */
    public function notes()
    {
        return $this->hasMany(ClientNote::class)->latest();
    }

    /**
     * تسمية نوع العميل حسب لغة الواجهة.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'individual' => __('Individual'),
            'company' => __('Company'),
            'government' => __('Government Entity'),
            default => $this->type ?? '',
        };
    }

    /**
     * تسمية حالة العميل حسب لغة الواجهة.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => __('Active'),
            'inactive' => __('Inactive'),
            default => $this->status ?? '',
        };
    }
}
