<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Classes') }} - {{$class->class_name}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Students In Classes') }}</h1>
                            <p class="mt-2 text-sm text-gray-700">A list of all the {{ __('Students In Classes') }}.</p>
                        </div>
                    </div>

                    <div class="mt-2 m:flex items-center">
                        <div class="sm:flex-auto">
                            <div class="sm:flex-auto">
                                <select class="w-full" id="user_id"></select>
                            </div>

                            <a id="btn-add" type="button" href="javascript:void(0)" class="mt-2 block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add new</a>
                        </div>
                        
                    </div>

                    <div class="flow-root">
                        <div class="mt-8 overflow-x-auto">
                            <div class="inline-block min-w-full py-2 align-middle">
                                <table class="w-full divide-y divide-gray-300">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">No</th>
                                        <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Student Id</th>
                                        <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Student Name</th>
                                        <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Join Date</th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
                                    </tr>
                                    </thead>
                                    <tbody id="body-students" class="divide-y divide-gray-200 bg-white">
                                        @foreach ($students as $student)
                                            <tr class="even:bg-gray-50 row-{{$student->id}}"">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-semibold text-gray-900">{{ ++$i }}</td>

                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $student->user->id }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $student->user->name }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $student->start_on }}</td>

                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                                <a data-id="{{$student->id}}" href="javascript:void(0)" class="btn-delete text-red-600 font-bold hover:text-red-900">{{ __('Delete') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                const loadingspinner = $(`#loading-spinner`);
                const studentsTag = $(`#body-students`);
                const classId = `{{$class->id}}`;
                const userIdTag = $(`#user_id`);
                userIdTag.select2({
                    placeholder: 'Search student name',
                    ajax: {
                        url: '{{route("classes.manage.student.search", ["id"=>$class->id])}}',
                        dataType: 'json'
                    }
                });

                $(`#btn-add`).click(async function(){
                    let userId = userIdTag.val();

                    if(userId == ''){
                        return;
                    }

                    loadingspinner.css("visibility", "visible");
                    let formData = new FormData();
                    formData.append('user_id', userId);
                    formData.append('class_id', classId);
                    let response = await fetch(`/dashboard/classes/manage-students/store`, {
                        headers: {
                            "X-CSRF-Token": `{{csrf_token()}}`,
                            // "Content-Type": "application/json"
                        },
                        method: "POST",
                        body: formData,
                    });
                    let json = await response.json();
                    let newRow = `
                        <tr class="even:bg-gray-50 row-${studentsTag.id}">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-semibold text-gray-900">${studentsTag.children().length+1}</td>

                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${json.data.studentsInClass.user_id}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${json.data.user.name}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${json.data.studentsInClass.start_on}</td>

                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                            <a data-id="${json.data.studentsInClass.id}" href="javascript:void(0)" class="btn-delete text-red-600 font-bold hover:text-red-900">{{ __('Delete') }}</a>
                            </td>
                        </tr>
                    `;
                    studentsTag.append(newRow);

                    loadingspinner.css("visibility", "hidden");
                    userIdTag.val(null).trigger('change');
                });

                $(document).on('click', '.btn-delete', async function(){
                    if(!confirm('Are you sure???')){
                        return;
                    }
                    const rowId = $(this).data('id');
                    loadingspinner.css("visibility", "visible");
                    let response = await fetch(`/dashboard/classes/manage-students/destroy/${rowId}`, {
                        headers: {
                            "X-CSRF-Token": `{{csrf_token()}}`,
                        },
                        method: "DELETE"
                    });
                    let json = await response.json();
                    $(`.row-${rowId}`).remove();
                    loadingspinner.css("visibility", "hidden");
                    return;
                });

            });
        </script>
    </x-slot>

</x-app-layout>