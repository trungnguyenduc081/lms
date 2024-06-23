<div class="space-y-6">
    
    <div>
        <x-input-label for="class_id" :value="__('Class Id')"/>
        <select class="mt-1 block w-full" id="class_id" name="class_id">
            @if(isset($defaultClass) && $defaultClass)
                <option value="{{$defaultClass['id']}}" selected>{{$defaultClass['text']}}</option>
            @endif
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('class_id')"/>
    </div>
    <div>
        <x-input-label for="title" :value="__('Title')"/>
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $assignment?->title)" autocomplete="title" placeholder="Title"/>
        <x-input-error class="mt-2" :messages="$errors->get('title')"/>
    </div>
    <div>
        <x-input-label for="description" :value="__('Description')"/>
        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description', $assignment?->description)" autocomplete="description" placeholder="Description"/>
        <x-input-error class="mt-2" :messages="$errors->get('description')"/>
    </div>
    <div>
        <x-input-label for="due_date" :value="__('Due Date')"/>
        <x-text-input id="due_date" name="due_date" type="text" class="mt-1 block w-full flatpickr" :value="old('due_date', $assignment?->due_date)" autocomplete="due_date" placeholder="Due Date"/>
        <x-input-error class="mt-2" :messages="$errors->get('due_date')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>