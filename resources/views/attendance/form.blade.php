<div class="space-y-6">
    
    <div>
        <x-input-label for="user_id" :value="__('Student')"/>
        <select class="mt-1 block w-full" id="user_id" name="user_id">
            @if($defaultStudent)
                <option value="{{$defaultStudent['id']}}" selected>{{$defaultStudent['text']}}</option>
            @endif
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('user_id')"/>
    </div>
    <div>
        <x-input-label for="class_id" :value="__('Class')"/>
        <select class="mt-1 block w-full" id="class_id" name="class_id">
            @if($defaultClass)
                <option value="{{$defaultClass['id']}}" selected>{{$defaultClass['text']}}</option>
            @endif
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('class_id')"/>
    </div>
    <div>
        <x-input-label for="date" :value="__('Date')"/>
        <x-text-input id="date" name="date" type="text" class="mt-1 block w-full flatpickr" :value="old('date', $attendance?->date)" autocomplete="date" placeholder="Date"/>
        <x-input-error class="mt-2" :messages="$errors->get('date')"/>
    </div>
    <div>
        <x-input-label for="status" :value="__('Status')"/>
        <x-select name="status" :options="[''=>'Choose status', 1=>'On Time', '0'=>'Late', -1=>'Absent']" class="select2-multiple mt-1 block w-full" :value="old('status', $attendance?->status)"/>
        <x-input-error class="mt-2" :messages="$errors->get('status')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>