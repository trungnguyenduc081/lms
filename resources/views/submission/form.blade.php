<div class="space-y-6">
    
    <div>
        <x-input-label for="assignment_id" :value="__('Assignment Id')"/>
        <select id="assignment_id" name="assignment_id" type="text" class="mt-1 block w-full">
            @if($defaultAssignment)
                <option value="{{$defaultAssignment->id}}" selected>{{$defaultAssignment->title}}</option>
            @endif
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('assignment_id')"/>
    </div>
    <div>
        <x-input-label for="student_id" :value="__('Student')"/>
        <select id="student_id" name="student_id" type="text" class="mt-1 block w-full">
            @if($defaultStudent)
                <option value="{{$defaultStudent->id}}" selected>{{$defaultStudent->name}}</option>
            @endif
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('student_id')"/>
    </div>
    <div>
        <x-input-label for="submission_time" :value="__('Submission Time')"/>
        <x-text-input id="submission_time" name="submission_time" type="text" class="mt-1 block w-full flatpickrtime" :value="old('submission_time', $submission?->submission_time)" autocomplete="submission_time" placeholder="Submission Time"/>
        <x-input-error class="mt-2" :messages="$errors->get('submission_time')"/>
    </div>
    <div>
        <x-input-label for="files" :value="__('Files')"/>
        <x-file name="files" multiple="multiple"/>
        <x-input-error class="mt-2" :messages="$errors->get('files')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>