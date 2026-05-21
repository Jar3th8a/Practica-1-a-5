<form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="attachments">Archivos (máx 5):</label>

        <input
            type="file"
            id="attachments"
            name="attachments[]"
            multiple
            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
        >

        @error('attachments')
            <div class="text-red-600">{{ $message }}</div>
        @enderror

        @foreach (($errors->get('attachments.*')) as $message)
            <div class="text-red-600">{{ $message }}</div>
        @endforeach
    </div>

    <button type="submit">Subir</button>
</form>

