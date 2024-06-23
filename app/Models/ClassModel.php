<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Class
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 * @property $class_name
 * @property $course_id
 * @property $teacher_id
 * @property $schedule_from
 * @property $schedule_to
 * @property $status
 * @property $exclude_dates
 *
 * @property Course $course
 * @property User $user
 * @property Assignment[] $assignments
 * @property Attendance[] $attendances
 * @property ClassChange[] $classChanges
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ClassModel extends Model
{
    protected $table = 'classes';

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['class_name', 'course_id', 'teacher_id', 'schedule_from', 'schedule_to', 'status', 'exclude_dates'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exclude_dates' => 'array',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'course_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignments()
    {
        return $this->hasMany(\App\Models\Assignment::class, 'class_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance::class, 'class_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classChanges()
    {
        // return $this->hasMany(\App\Models\ClassChange::class, 'id', 'class_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentsInClass()
    {
        return $this->hasMany(\App\Models\StudentsInClass::class, 'class_id', 'id');
    }
    
}
