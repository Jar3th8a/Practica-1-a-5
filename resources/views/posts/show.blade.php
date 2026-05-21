<h1>Post</h1>

@if(session('success'))
    <div style="color: green">{{ session('success') }}</div>
@endif

<h2>{{ $post->title ?? 'Sin título' }}</h2>
<p>{{ $post->content ?? '' }}</p>

<h3>Archivos</h3>

@if($post->attachments->count())
    @foreach($post->attachments as $file)
        <div class="attachment" style="margin-bottom: 8px;">
            <a href="{{ asset('storage/' . $file->path) }}" target="_blank" rel="noopener">
                {{ $file->original_name }}
            </a>
            <small>({{ number_format($file->size / 1024, 2) }} KB)</small>

            <form action="{{ route('attachments.destroy', $file) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        </div>
    @endforeach
@else
    <p>No hay archivos.</p>
@endif
