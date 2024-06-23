<div class="space-y-6">
    
    <div>
        <x-input-label for="name" :value="__('Full name')"/>
        <x-text-input id="user_id" name="name" type="text" class="mt-1 block w-full" :value="old('name', $teacher?->user?->name)" autocomplete="name" placeholder="Full name"/>
        <x-input-error class="mt-2" :messages="$errors->get('user_id')"/>
    </div>

    <div>
        <x-input-label for="subject_taught" :value="__('Subject Taught')"/>
        <x-text-input id="subject_taught" name="subject_taught" type="text" class="mt-1 block w-full" :value="old('subject_taught', $teacher?->subject_taught)" autocomplete="subject_taught" placeholder="Subject Taught"/>
        <x-input-error class="mt-2" :messages="$errors->get('subject_taught')"/>
    </div>

    <div>
        <x-input-label for="birthday" :value="__('Birthday')"/>
        <x-text-input id="birthday" name="birthday" type="date" class="mt-1 block w-full" :value="old('birthday', $teacher?->birthday)" autocomplete="birthday" placeholder="Birthday"/>
        <x-input-error class="mt-2" :messages="$errors->get('birthday')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>

<x-slot name="scripts">
    <script>
        window.addEventListener("load", (event)=>{
            flatpickr("input[type='date']", {maxDate:'today', allowInput:false});
        });
    </script>
</x-slot>