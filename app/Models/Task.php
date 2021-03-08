<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'mailable_at'
    ];

    /**
     * Users responsible for this task (only applicable for users with role = ROLE_USER)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('finished', 'completed');
    }
}
