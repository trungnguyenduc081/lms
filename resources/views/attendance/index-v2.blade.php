<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendances') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto mb-2">
                            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Attendances') }}</h1>
                            <p class="mt-2 text-sm text-gray-700">Choose class and date.</p>
                        </div>
                        <!-- <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            <a type="button" href="{{ route('attendances.create') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add new</a>
                        </div> -->
                    </div>

                    <div class="">
                        <form method="get">
                            <div class="mb-4">
                                <x-input-label for="class_id" :value="__('Class')"/>
                                <select id="class_id" name="class" class="select2-multiple mt-1 block w-full rounded-md">
                                    @if(isset($class))
                                        <option value="{{$class->id}}">{{$class->class_name}}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-4">
                                <x-input-label for="date" :value="__('Date')"/>
                                <x-text-input id="date" name="date" type="text" class="mt-1 block w-full flatpickr" :value="old('date', $date ?? '')" autocomplete="date" placeholder="Date"/>
                            </div>
                            <x-primary-button class="mt-2">Apply</x-primary-button>
                        </form>
                    </div>

                    @if(isset($class) && $students)

                    <div class="mt-8">
                        <div class="mb-2">
                            <p class="mt-2 text-sm text-gray-700">Class: <strong>{{$class->class_name}}</strong></p>
                            <p class="mt-2 text-sm text-gray-700">Class schedule from: <strong>{{$class->schedule_from}}</strong></p>
                            <p class="mt-2 text-sm text-gray-700">Number of students: <strong>{{count($students)}}</strong></p>
                        </div>

                        <x-text-input id="search-list" type="text" class="mt-1 block w-full search" placeholder="Search students by name"/>
                    </div>
                    <div class="flow-root">
                        <form method="POST" action="{{ route('attendances.v2.update') }}"  role="form" enctype="multipart/form-data">
                            <input type="hidden" name="date" value="{{$date}}"/>
                            <input type="hidden" name="class" value="{{$class->id}}"/>
                            <div class="mt-2 overflow-x-auto">
                                <div id="student-list" class="inline-block min-w-full py-2 align-middle sm:grid-rows-none md:grid grid-cols-3 gap-4">
                                    @foreach($students as $student)
                                        @php
                                        $log = $attendanceLogs->where('user_id', $student->user->id)->first();
                                        @endphp
                                        <div class="border-2 p-2">
                                            <p class="mb-3">{{$student->user->id}}: <strong class="student-name">{{$student->user->name}}</strong></p>
                                            <x-select :value="$log->status ?? ''" name="status[{{$student->user->id}}]" :options="[''=>'Choose status', 1=>'On Time', '0'=>'Late', -1=>'Absent']" class="select2-multiple mt-1 block w-full rounded-md mb-4"/>
                                            <x-text-input :value="$log->note ?? ''" name="note[{{$student->user->id}}]" max="500" type="text" class="mt-1 block w-full" autocomplete="class_id" placeholder="Note"/>
                                        </div>
                                    @endforeach
                                </div>
                                @csrf
                                <x-primary-button>Submit</x-primary-button>
                            
                            </div>
                        </form>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            window.addEventListener("load", (event)=>{

                function jsSearch() {
                    var input, filter, ul, li, a, i, txtValue;
                    input = document.getElementById('search-list');
                    filter = input.value.toUpperCase();
                    ul = document.getElementById("student-list");
                    li = ul.querySelectorAll('.student-name');

                    for (i = 0; i < li.length; i++) {
                        txtValue = li[i].textContent || li[i].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            li[i].parentElement.parentElement.style.display = '';
                        } else {
                            li[i].parentElement.parentElement.style.display = 'none';
                        }
                    }
                }

                $(`#search-list`).keyup(function(){
                    jsSearch();
                });

                const classSelectTag = $(`#class_id`);
                classSelectTag.select2({
                    placeholder: 'Search class by class name',
                    ajax: {
                        url: '{{route("classes.select2")}}',
                        dataType: 'json'
                    }
                });

                classSelectTag.on('select2:select', async function (e) {
                    let val = $(this).val();
                    if(val == ''){
                        return;
                    }

                    await loadDatePicker(val);
                });

                async function loadDatePicker(val){
                    const response = await fetch(`/dashboard/classes/detail/${val}`);
                    const dataResponse = await response.json();
                    const classRecord = dataResponse.data.class;
                    let scheduleFrom = classRecord.schedule_from;
                    let scheduleTo = classRecord.schedule_to;
                    let datePickerConfig = {minDate:`${scheduleFrom}`, allowInput:false};
                    if(scheduleTo != null){
                        datePickerConfig.maxDate = scheduleTo;
                    }

                    let disabledEveryDays = [];
                    let disabledSpecificdays = [];
                    let disableConfigs = [];
                    if(classRecord.exclude_dates != null){
                        for (let index = 0; index < (classRecord.exclude_dates).length; index++) {
                            const excludeDate = (classRecord.exclude_dates)[index];
                            console.log(excludeDate);
                            switch(excludeDate){
                                case "every_sat":
                                    disabledEveryDays.push(6);
                                    break;
                                case "every_sun":
                                    disabledEveryDays.push(0);
                                    break;
                                default:
                                    disabledSpecificdays.push(excludeDate);
                                break;
                            }
                        }
                    }

                    if(disabledEveryDays.length > 0){
                        disableConfigs.push(function(date){
                            return (disabledEveryDays.indexOf(date.getDay()) !== -1);
                        });
                    }

                    if(disabledEveryDays.length > 0){
                        disableConfigs.push(function(date) {
                            return disabledSpecificdays.includes(date.toISOString().substring(0, 10));
                        });
                    }
                    
                    if(disableConfigs.length > 0){
                        datePickerConfig.disable = disableConfigs;
                    }
                    
                    flatpickr("input.flatpickr",datePickerConfig);
                }
                @if(!is_null($class))
                    ( async()=>{
                        await loadDatePicker({{$class->id}});
                    })();
                @else
                    flatpickr("input.flatpickr", {allowInput:false});
                @endif
            });
        </script>
    </x-slot>
</x-app-layout>