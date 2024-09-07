@extends('layouts.app')

@section('content')
    <h1>ROOM {{ $room->uuid }}</h1>
    @include('components.games.invite', ['$room' => $room])
    @include('components.games.create', ['$room' => $room])
    @foreach($room->games as $game)
        {{ $game->initializer_id === auth()->user()->id ? 'You' : 'The other' }} should play first.
        @include('components.games.board', ['room'=> $room, 'game' => $game])
    @endforeach
@endsection
