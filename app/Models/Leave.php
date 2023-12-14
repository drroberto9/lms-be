<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Leave extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'assigned_to',
        'approved_by',
        'start_date',
        'end_date',
        'date_approved',
        'type',
        'status',
        'remarks',
        'attachment_name',
        'attachment_type',
        'attachment_size'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
