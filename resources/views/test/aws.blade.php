<!DOCTYPE html>
<html>
<head>
    <title>Upload File to S3</title>
</head>
<body>
    <h1>Upload File to S3</h1>
    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="file">Choose file:</label>
            <input type="file" id="file" name="file" required>
        </div>
        <div>
            <button type="submit">Upload</button>
        </div>
    </form>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('error'))
    <p>{{ session('error') }}</p>
@endif
</body>
</html>
