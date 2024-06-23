<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update') }} Attendance
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Update') }} Attendance</h1>
                            <p class="mt-2 text-sm text-gray-700">Update existing {{ __('Attendance') }}.</p>
                        </div>
                        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            <a type="button" href="{{ route('attendances.index') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Back</a>
                        </div>
                    </div>

                    <div class="flow-root">
                        <div class="mt-8 overflow-x-auto">
                            <div class="max-w-xl py-2 align-middle">
                                <form method="POST" action="{{ route('attendances.update', $attendance->id) }}"  role="form" enctype="multipart/form-data">
                                    {{ method_field('PATCH') }}
                                    @csrf
                                    @include('attendance.form')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            window.addEventListener("load", (event)=>{

                $(`#user_id`).select2({
                    placeholder: 'Search student name',
                    ajax: {
                        url: '{{route("students.select2")}}',
                        dataType: 'json'
                    }
                });

                const classSelectTag = $(`#class_id`);
                classSelectTag.select2({
                    placeholder: 'Search class by class name, teacher name, or course name',
                    ajax: {
                        url: '{{route("attendances.search.class")}}',
                        dataType: 'json'
                    }
                });

                classSelectTag.on('select2:select', async function (e) {
                    let val = $(this).val();
                    if(val == ''){
                        return;
                    }
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
                });

                flatpickr("input.flatpickr", {maxDate:'today', allowInput:false});
            });
        </script>
    </x-slot>

</x-app-layout>
