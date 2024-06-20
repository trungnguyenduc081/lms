<div class="space-y-6">
    
    <div>
        <x-input-label for="course_name" :value="__('Course Name')"/>
        <x-text-input id="course_name" name="course_name" type="text" class="mt-1 block w-full" :value="old('course_name', $course?->course_name)" autocomplete="course_name" placeholder="Course Name"/>
        <x-input-error class="mt-2" :messages="$errors->get('course_name')"/>
    </div>
    <div>
        <x-input-label for="description" :value="__('Description')"/>
        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description', $course?->description)" autocomplete="description" placeholder="Description"/>
        <x-input-error class="mt-2" :messages="$errors->get('description')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>