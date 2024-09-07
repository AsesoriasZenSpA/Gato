@extends('layouts.app')

@section('content')
    <h1>DASHBOARD</h1>

    @if(auth()->check())
        Hola {{ auth()->user()->name }}
    @endif
    <div>
        <form action="{{ route('rooms.create') }}" method="POST">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-primary">Crear Sala</button>
        </form>
    </div>
@endsection
