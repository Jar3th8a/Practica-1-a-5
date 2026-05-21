<h1>Crear Post</h1>

<form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="title">Título</label>
        <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        @error('title')
            <div class="text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="content">Contenido</label>
        <textarea id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
        @error('content')
            <div class="text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="category_id">Categoría</label>
        <select id="category_id" name="category_id" required>
            <option value="">Selecciona una categoría</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="text-red-600">{{ $message }}</div>
        @enderror
    </div>

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

    <button type="submit">Guardar y subir archivos</button>
</form>
