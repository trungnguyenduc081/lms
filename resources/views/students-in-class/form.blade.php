<div class="space-y-6">
    
    <div>
        <x-input-label for="user_id" :value="__('User Id')"/>
        <x-text-input id="user_id" name="user_id" type="text" class="mt-1 block w-full" :value="old('user_id', $studentsInClass?->user_id)" autocomplete="user_id" placeholder="User Id"/>
        <x-input-error class="mt-2" :messages="$errors->get('user_id')"/>
    </div>
    <div>
        <x-input-label for="class_id" :value="__('Class Id')"/>
        <x-text-input id="class_id" name="class_id" type="text" class="mt-1 block w-full" :value="old('class_id', $studentsInClass?->class_id)" autocomplete="class_id" placeholder="Class Id"/>
        <x-input-error class="mt-2" :messages="$errors->get('class_id')"/>
    </div>
    <div>
        <x-input-label for="start_on" :value="__('Start On')"/>
        <x-text-input id="start_on" name="start_on" type="text" class="mt-1 block w-full" :value="old('start_on', $studentsInClass?->start_on)" autocomplete="start_on" placeholder="Start On"/>
        <x-input-error class="mt-2" :messages="$errors->get('start_on')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>