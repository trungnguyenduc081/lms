<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Submission
 *
 * @property $id
 * @property $assignment_id
 * @property $student_id
 * @property $submission_time
 * @property $files
 * @property $created_at
 * @property $updated_at
 *
 * @property Assignment $assignment
 * @property User $user
 * @property Grade[] $grades
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Submission extends Model
{
    use HasFactory;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['assignment_id', 'student_id', 'submission_time', 'files', 'feedback'];

    protected function casts(): array
    {
        return [
            'files' => 'array',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignment()
    {
        return $this->belongsTo(\App\Models\Assignment::class, 'assignment_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id', 'id');
    }
    
}
