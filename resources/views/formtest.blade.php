<!DOCTYPE html>
<html>
<head>
    <title>Form Test</title>
</head>
<body>
    <form action="{{ route('uploadImage') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="images[]" multiple>
        <select name="format">
            <option value="png">PNG</option>
            <option value="jpeg">JPEG</option>
            <option value="gif">GIF</option>
        </select>
        <button type="submit">Submit</button>
    </form>
</body>
</html>