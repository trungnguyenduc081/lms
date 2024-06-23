<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create') }} Class
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Create') }} Class</h1>
                            <p class="mt-2 text-sm text-gray-700">Add a new {{ __('Class') }}.</p>
                        </div>
                        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            <a type="button" href="{{ route('classes.index') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Back</a>
                        </div>
                    </div>

                    <div class="flow-root">
                        <div class="mt-8 overflow-x-auto">
                            <div class="max-w-xl py-2 align-middle">
                                <form method="POST" action="{{ route('classes.store') }}"  role="form" enctype="multipart/form-data">
                                    @csrf

                                    @include('class.form')
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
                $(`.select2-multiple`).select2({
                    placeholder: 'Select options',
                    tags:true
                });

                $(`#course_id`).select2({
                    placeholder: 'Search course name',
                    ajax: {
                        url: '{{route("courses.select2")}}',
                        dataType: 'json'
                    }
                });

                $(`#teacher_id`).select2({
                    placeholder: 'Search teacher name',
                    ajax: {
                        url: '{{route("teachers.select2")}}',
                        dataType: 'json'
                    }
                });

                flatpickr("input.flatpickr", {maxDate:'today', allowInput:false});
            });
        </script>
    </x-slot>

</x-app-layout>