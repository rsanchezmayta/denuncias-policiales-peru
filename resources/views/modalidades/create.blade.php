@extends('layouts.app')

@section('title', 'Nueva modalidad')

@section('content')
    <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Nueva modalidad</h1>

    <form method="POST" action="{{ route('modalidades.store') }}"
          class="bg-white dark:bg-slate-900 rounded-xl shadow p-6 mt-6 max-w-xl space-y-4 dark:border dark:border-slate-800">
        @include('modalidades._form')
    </form>
@endsection
