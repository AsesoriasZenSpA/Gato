@php use App\Models\User; @endphp
@extends('layouts.app')

@section('content')
    <h1>ROOM {{ $room->uuid }}</h1>
    <form action="{{ route('rooms.invite', $room->uuid) }}" method="POST">
        {{ csrf_field() }}
        <select name="guest_id" id="guest_id">
            @foreach(User::all() as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary"> Invite </button>
    </form>

    <form action="{{ route('rooms.games.create', $room->uuid) }}" method="POST">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-primary"> Create Game </button>
    </form>

    @foreach($room->games as $game)
        <pre>{{ json_encode($game->structure) }}</pre>

        {{ $game->initializer_id === auth()->user()->id ? 'You' : 'The other' }} should play first.
        <table class="table mb-5" >
            <tbody>
            @foreach($game->structure as $i => $row)
                <tr>
                    @foreach($row as $e => $column)
                        @if($column == " ")
                            <td>
                                <form action="{{ route('rooms.games.play', [$room->uuid, $game]) }}" method="POST" >
                                    {{ csrf_field() }}
                                    <input type="hidden" name="position" value="{{ ($i * 3) + $e }}">
                                    <button type="submit" class="btn btn-primary">Play</button>
                                </form>
                            </td>
                        @else
                            <td>
                                {{ $column }}
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
@endsection
