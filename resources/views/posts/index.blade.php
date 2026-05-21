<h1>Posts</h1>

<p><a href="{{ route('posts.create') }}">Crear nuevo post</a></p>

@if($posts->isEmpty())
    <p>No hay posts creados.</p>
@else
    <ul>
        @foreach($posts as $post)
            <li>
                <a href="{{ route('posts.show', $post) }}">
                    {{ $post->title }}
                </a>
                <small>({{ $post->attachments->count() }} archivos)</small>
            </li>
        @endforeach
    </ul>
@endif
