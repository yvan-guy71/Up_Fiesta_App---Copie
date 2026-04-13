<div>
    <h3>Dernières activités</h3>
    <ul>
        @foreach($logs as $log)
            <li>
                <strong>{{ $log->user?->name ?? 'Système' }}</strong> :
                {{ $log->action }}
                <span class="text-muted">({{ $log->created_at->diffForHumans() }})</span>
                <br>
                <small>{{ $log->description }}</small>
            </li>
        @endforeach
    </ul>
</div>
