@extends('vpanel::layouts.auth')

@section('content')

    @if ($errors->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            <span class="font-medium">{{ $errors->first('error') }}</span>
        </div>
    @endif

    <form method="post" action="{{ route('login.perform') }}" class="shadow-md rounded px-8 pt-6 pb-8 bg-gray-50">
        @csrf
        <div class="mb-5">
            <h1 class="text-2xl">Войти</h1>
        </div>

        <div class="mb-6">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Логин</label>
            <input type="text" id="login" name="login" value="{{ old('login') }}"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                   required>
        </div>

        <div class="mb-6">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Пароль</label>
            <input type="password" id="password" name="password"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                   required>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">
                Войти
            </button>
            <a href="{{ route('restore.show') }}"
               class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Забыли пароль?
            </a>
        </div>
    </form>
@endsection