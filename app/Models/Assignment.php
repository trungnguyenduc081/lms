<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Assignment
 *
 * @property $id
 * @property $class_id
 * @property $title
 * @property $description
 * @property $due_date
 * @property $created_at
 * @property $updated_at
 *
 * @property Class $class
 * @property Submission[] $submissions
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Assignment extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['class_id', 'title', 'description', 'due_date'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(\App\Models\ClassModel::class, 'class_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(\App\Models\Submission::class, 'assignment_id', 'id');
    }
    
}
