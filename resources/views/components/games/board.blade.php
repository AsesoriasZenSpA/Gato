<table class="table mb-5" >
    <tbody>
    @foreach($game->structure as $i => $row)
        <tr>
            @foreach($row as $e => $column)
                @if($column == " ")
                    <td>
                        @include('components.games.play', ['room' => $room, 'game' => $game, 'i' => $i, 'e' => $e])
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
