<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Department extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_name',
        'employment_status',
        'date_of_employment',
        'job_title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
