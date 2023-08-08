@extends('vpanel::layouts.auth')

@section('content')

    @if (Session::has('message'))
        <div class="mb-4 text-sm bg-gray-100" role="alert">
            <span class="font-medium text-green-500">{{ Session::get('message') }}</span>
        </div>
    @endif

    @if ($errors->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            <span class="font-medium">{{ $errors->first('error') }}</span>
        </div>
    @endif

    <form method="post" action="{{ route('reset.perform') }}" class="shadow-md rounded px-8 pt-6 pb-8 bg-gray-50">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-5">
            <h1 class="text-2xl">Придумайте новый пароль</h1>
        </div>

        <div class="mb-6">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
            <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            @error('email')
                <div class="text-sm text-red-700 mt-1">Некорректный email!</div>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Пароль</label>
            <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            @error('password')
                <div class="text-sm text-red-700 mt-1">Минимальная длина пароля 6 символов!</div>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Повторите пароль</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
            @error('password_confirmation')
                 <div class="text-sm text-red-700 mt-1">Пароли не совпадают!</div>
            @enderror
        </div>

        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Восстановить</button>
    </form>
@endsection