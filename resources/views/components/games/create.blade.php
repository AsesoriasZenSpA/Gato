<form action="{{ route('rooms.games.create', $room->uuid) }}" method="POST">
    {{ csrf_field() }}
    |        <button type="submit" class="btn btn-primary"> Create Game </button>
</form>
