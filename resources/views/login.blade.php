@extends('layouts.app')

@section('content')
    <h1>LOGIN</h1>

    <div>
        <form action="{{ route('login') }}" method="POST">
            {{ csrf_field() }}
            <input type="email" id="email" name="email">
            <input type="password" id="password" name="password">
            <button type= "submit" class="btn btn-primary">Login</button>
        </form>
    </div>
@endsection
