<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submissions') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto mb-2">
                            <!-- <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Submissions') }}</h1> -->
                            <p class="mt-2 text-sm text-gray-700">Choose one assignment to show the students.</p>
                        </div>
                    </div>

                    <div class="">
                        <form method="get">
                            <div class="mb-4">
                                <x-input-label for="assignment_id" class="mb-1" :value="__('Assignment')"/>
                                <select id="assignment_id" name="assignment_id" class="select2-multiple mt-1 block w-full rounded-md">
                                    @if(isset($assignment))
                                        <option value="{{$assignment->id}}">{{$assignment->title}}</option>
                                    @endif
                                </select>
                            </div>
                            <x-primary-button class="mt-2">Apply</x-primary-button>
                        </form>
                    </div>

                    @if(isset($class) && $students)
                    <div class="mt-8">
                        <div class="mb-2">
                            <p class="mt-2 text-sm text-gray-700">Class: <strong>{{$class->class_name}}</strong></p>
                            <p class="mt-2 text-sm text-gray-700">Asignment Due Date: <strong>{{$assignment->due_date}}</strong></p>
                            <!-- <p class="mt-2 text-sm text-gray-700">Class schedule from: <strong>{{$class->schedule_from}}</strong></p> -->
                            <p class="mt-2 text-sm text-gray-700">Number of students: <strong>{{count($students)}}</strong></p>
                        </div>

                        <x-text-input id="search-list" type="text" class="mt-1 block w-full search" placeholder="Search students by name"/>
                    </div>
                    <div class="flow-root">
                        <form method="POST" action="{{route('submissions.multiple-storing')}}" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="assignment_id" value="{{$assignment->id}}"/>
                            <div class="mt-8 overflow-x-auto">
                                <div id="student-list" class="inline-block min-w-full py-2 align-middle sm:grid-rows-none md:grid grid-cols-2 gap-8">
                                    @foreach($students as $student)
                                        @php
                                        $log = $submissionLogs->where('student_id', $student->user->id)->first();
                                        @endphp
                                        <div class="border-2 p-2">
                                            <p class="mb-3">{{$student->user->id}}: <strong class="student-name">{{$student->user->name}}</strong></p>
                                            <div class="mt-2">
                                                <x-input-label :value="__('Grade')"/>
                                                <x-text-input  :value="$log->grade ?? ''" name="grade[{{$student->user->id}}]" max="500" type="text" class="mt-2 block w-full" placeholder="Grade"/>
                                            </div>
                                            <div class="mt-2">
                                                <x-input-label :value="__('Feedback')"/>
                                                <x-textarea name="feedback[{{$student->user->id}}]" class="mt-2 block w-full" placeholder="Feedback" rows="5">{{$log->feedback ?? ''}}</x-textarea>
                                            </div>
                                            <div class="mt-2">
                                                <x-input-label :value="__('Files(only allowed images)')"/>
                                                <x-file accept="image/*" name="files_{{$student->user->id}}[]" multiple="multiple"/>
                                                @if($log && is_array($log->files))
                                                
                                                    <ul class="mt-3 list-decimal p-5">
                                                    @foreach($log->files as $f)
                                                        <li class="mb-1"><a target="_blank" title="Click here to view file" class="text-green-600" href="{{route('file.preview', ['path'=>base64_encode($f)])}}">{{basename($f)}}</a></li>
                                                    @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($assignment->due_date < date('Y-m-d'))
                                    @csrf
                                    <x-primary-button>Submit</x-primary-button>
                                @endif
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

                const classSelectTag = $(`#assignment_id`);
                classSelectTag.select2({
                    placeholder: 'Search Assignments by title or Class name',
                    ajax: {
                        url: '{{route("submissions.search.assignments.keyword")}}',
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