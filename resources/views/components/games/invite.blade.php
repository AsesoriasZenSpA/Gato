@php use App\Models\User; @endphp

@if(!$room->guest_id)
    <form action="{{ route('rooms.invite', $room->uuid) }}" method="POST">
        {{ csrf_field() }}
        <select name="guest_id" id="guest_id">
            @php $auth_id = auth()->user()->id; @endphp
            @foreach(User::all() as $user)
                @if($user->id !== $auth_id)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endif
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary"> Invite </button>
    </form>
@endif
