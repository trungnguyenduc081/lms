<div class="space-y-6">
    
    <div>
        <x-input-label for="user_id" :value="__('Full name')"/>
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $student?->user?->name)" placeholder="Full name"/>
        <x-input-error class="mt-2" :messages="$errors->get('user_id')"/>
    </div>

    <div>
        <x-input-label for="enrollment_date" :value="__('Enrollment Time')"/>
        <x-text-input id="enrollment_date" name="enrollment_date" type="date" class="mt-1 block w-full flatpickr" :value="old('enrollment_date', $student?->enrollment_date)" autocomplete="enrollment_date" placeholder="Enrollment Time"/>
        <x-input-error class="mt-2" :messages="$errors->get('enrollment_date')"/>
    </div>
    
    <div>
        <x-input-label for="birthday" :value="__('Birthday')"/>
        <x-text-input id="birthday" name="birthday" type="date" class="mt-1 block w-full flatpickr" :value="old('birthday', $student?->birthday)" autocomplete="birthday" placeholder="Birthday"/>
        <x-input-error class="mt-2" :messages="$errors->get('birthday')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>

<x-slot name="scripts">
    <script>
        window.addEventListener("load", (event)=>{
            flatpickr("input.flatpickr", {maxDate:'today', allowInput:false});
        });
    </script>
</x-slot>