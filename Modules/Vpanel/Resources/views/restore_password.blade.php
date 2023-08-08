@extends('vpanel::layouts.auth')

@section('content')

    @if (Session::has('message'))
        <div class="mb-4 text-sm bg-gray-100" role="alert">
            <span class="font-medium text-green-500">{{ Session::get('message') }}</span>
        </div>
    @endif

    <form method="post" action="{{ route('restore.perform') }}" class="shadow-md rounded px-8 pt-6 pb-8 bg-gray-50">
        @csrf
        <div class="mb-5">
            <h1 class="text-2xl">Восстановить пароль</h1>
        </div>

        <div class="mb-6">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
            <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Восстановить</button>
            <a href="{{ route('login.show') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Вернуться назад
            </a>
        </div>

    </form>
@endsection