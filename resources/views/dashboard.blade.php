<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="inline-block min-w-full py-2 align-middle sm:grid-rows-none md:grid grid-cols-3 gap-4 p-3">
                    <div class="border-2 p-2">
                        <p class="mb-3">Total Teachers: <strong>{{$totalTeachers}}</strong></p>
                    </div>
                    <div class="border-2 p-2">
                        <p class="mb-3">Total Students: <strong>{{$totalStudents}}</strong></p>
                    </div>
                    <div class="border-2 p-2">
                        <p class="mb-3">Total Class: <strong>{{$totalClasses}}</strong></p>
                    </div>
                    <div class="border-2 p-2">
                        <p class="mb-3">Total Assignments: <strong>{{$totalAssignments}}</strong></p>
                    </div>
                    <div class="border-2 p-2">
                        <p class="mb-3">Total Submissions: <strong>{{$totalSubmissions}}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
