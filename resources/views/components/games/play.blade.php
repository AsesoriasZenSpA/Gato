<form action="{{ route('rooms.games.play', [$room->uuid, $game]) }}" method="POST" >
    {{ csrf_field() }}
    <input type="hidden" name="position" value="{{ ($i * 3) + $e }}">
    <button type="submit" class="btn btn-primary">Play</button>
</form>
