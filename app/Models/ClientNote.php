<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'body',
        'created_by',
    ];

    /**
     * العميل
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * من أنشأ الملاحظة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
