<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create') }} Submission
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Create') }} Submission</h1>
                            <p class="mt-2 text-sm text-gray-700">Add a new {{ __('Submission') }}.</p>
                        </div>
                        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            <a type="button" href="{{ route('submissions.index') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Back</a>
                        </div>
                    </div>

                    <div class="flow-root">
                        <div class="mt-8 overflow-x-auto">
                            <div class="max-w-xl py-2 align-middle">
                                <form method="POST" action="{{ route('submissions.store') }}"  role="form" enctype="multipart/form-data">
                                    @csrf

                                    @include('submission.form')
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
            window.addEventListener("load", function(event){
                const assignmentTag = $(`#assignment_id`);
                assignmentTag.select2({
                    placeholder: 'Search assignment title or class name',
                    ajax: {
                        url: '{{route("submissions.search.assignments.keyword")}}',
                        dataType: 'json'
                    }
                });

                $(`#student_id`).select2({
                    placeholder: 'Search student name',
                    ajax: {
                        url: '{{route("submissions.search.students.keyword")}}',
                        dataType: 'json',
                        data: function (params) {
                            params.assignment_id = assignmentTag.val();
                            return params;
                        }
                    }
                });

                flatpickr("input.flatpickrtime", {allowInput:false, enableTime:true});
            });
        </script>
    </x-slot>
</x-app-layout>
