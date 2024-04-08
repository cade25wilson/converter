<!DOCTYPE html>
<html>
    <style>
        .hidden {
            display: none;
        }
    </style>
<head>
    <title>Form Test</title>
</head>
<body>
    <form action="{{ route('uploadImage') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="images[]" multiple>
        <select name="format">
            @foreach($formats as $format)
                <option value="{{ $format->id }}">{{ ($format->name) }}</option>
            {{-- <option value="png">PNG</option>
            <option value="jpeg">JPEG</option>
            <option value="gif">GIF</option> --}}
            @endforeach
        </select>
        {{-- <input type="checkbox" name="resize" value="1" id="resizecheck"> Resize --}}
        {{-- <input type="number" name="width" placeholder="Width px" class="hidden">
        <input type="number" name="height" placeholder="Height px" class="hidden"> --}}
        <button type="submit">Submit</button>
    </form>
</body>

<script>
    let checkbox = document.getElementById('resizecheck');
    let width = document.getElementsByName('width')[0];
    let height = document.getElementsByName('height')[0];
    // when checkbox is checked, show the width and height input fields
    checkbox.addEventListener('change', function() {
        if (checkbox.checked) {
            width.classList.remove('hidden');
            height.classList.remove('hidden');
        } else {
            width.classList.add('hidden');
            height.classList.add('hidden');
        }
    });
</script>

</html>