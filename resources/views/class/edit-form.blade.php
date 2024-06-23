<div class="space-y-6">
    
    <div>
        <x-input-label for="class_name" :value="__('Class Name')"/>
        <x-text-input id="class_name" name="class_name" type="text" class="mt-1 block w-full" :value="old('class_name', $class?->class_name)" autocomplete="class_name" placeholder="class_name"/>
        <x-input-error class="mt-2" :messages="$errors->get('class_name')"/>
    </div>
    <div>
        <x-input-label for="course_id" :value="__('Course')"/>
        <select class="mt-1 block w-full" id="course_id" name="course_id">
            @if($defaultCourse)
                <option value="{{$defaultCourse['id']}}" selected>{{$defaultCourse['text']}}</option>
            @endif
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('course_id')"/>
    </div>
    <div>
        <x-input-label for="teacher_id" :value="__('Teacher')"/>
        <select class="mt-1 block w-full" id="teacher_id" name="teacher_id">
            @if($defaultTeacher)
                <option value="{{$defaultTeacher['id']}}" selected>{{$defaultTeacher['text']}}</option>
            @endif
        </select>
        <!-- <x-text-input id="teacher_id" name="teacher_id" type="text" class="mt-1 block w-full" :value="old('teacher_id', $class?->teacher_id)" autocomplete="teacher_id" placeholder="Teacher Id"/> -->
        <x-input-error class="mt-2" :messages="$errors->get('teacher_id')"/>
    </div>
    <div>
        <x-input-label for="schedule_from" :value="__('Schedule From')"/>
        <x-text-input id="schedule_from" name="schedule_from" type="text" class="mt-1 block w-full" :value="old('schedule_from', $class?->schedule_from)" autocomplete="schedule_from" placeholder="Schedule From"/>
        <x-input-error class="mt-2" :messages="$errors->get('schedule_from')"/>
    </div>
    <div>
        <x-input-label for="schedule_to" :value="__('Schedule To')"/>
        <x-text-input id="schedule_to" name="schedule_to" type="text" class="mt-1 block w-full" :value="old('schedule_to', $class?->schedule_to)" autocomplete="schedule_to" placeholder="Schedule To"/>
        <x-input-error class="mt-2" :messages="$errors->get('schedule_to')"/>
    </div>
    <div>
        <x-input-label for="status" :value="__('Status')"/>
        <x-select name="status" :options="[1=>'Active', -1=>'Closed']" :value="old('status', $class?->status)"/>
        <x-input-error class="mt-2" :messages="$errors->get('status')"/>
    </div>
    <div>
        <x-input-label for="exclude_dates" :value="__('Exclude Dates').'(You can add a date with format for example: 2024-12-19)'"/>
        <x-select-exclude-dates multiple name="exclude_dates[]" :options="['every_sat'=>'Every Saturday']" class="select2-multiple mt-1 block w-full" :value="old('exclude_dates', $class?->exclude_dates)"/>
        <x-input-error class="mt-2" :messages="$errors->get('exclude_dates')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>