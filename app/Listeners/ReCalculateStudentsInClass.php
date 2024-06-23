<?php

namespace App\Listeners;

use App\Events\StudentInClassChanging;
use App\Models\ClassModel;
use App\Models\StudentsInClass;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReCalculateStudentsInClass
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StudentInClassChanging $event): void
    {
      
        $classId = $event->class_id;
        $studentsInClass = StudentsInClass::query()
        ->where('class_id', $classId)
        ->count();

        ClassModel::query()
        ->where('id', $classId)
        ->update([
            'students'=>$studentsInClass
        ]);

        return;
    }
}
