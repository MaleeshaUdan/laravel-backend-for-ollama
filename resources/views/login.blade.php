@extends('layouts.app')

@section('content')
<div class="flex min-h-screen flex-col justify-center px-6 py-12 lg:px-8 bg-slate-950">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <h2 class="mt-10 text-center text-3xl font-extrabold leading-9 tracking-tight text-white">Admin Login</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form class="space-y-6" action="{{ route('login') }}" method="POST">
      @csrf
      <div>
        <label for="username" class="block text-sm font-medium leading-6 text-slate-300">Username</label>
        <div class="mt-2">
          <input id="username" name="username" type="text" value="bingusala" required class="block w-full rounded-md border-0 bg-slate-800/50 py-2 pt-2 text-white shadow-sm ring-1 ring-inset ring-slate-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 px-3">
        </div>
        @error('username')
          <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="password" class="block text-sm font-medium leading-6 text-slate-300">Password</label>
        <div class="mt-2">
          <input id="password" name="password" type="password" value="bingusala123" required class="block w-full rounded-md border-0 bg-slate-800/50 py-2 pt-2 text-white shadow-sm ring-1 ring-inset ring-slate-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 px-3">
        </div>
        @error('password')
          <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 hover:scale-105 transition-all">Sign in</button>
      </div>
    </form>
  </div>
</div>
@endsection
