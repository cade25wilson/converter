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
    <form action="{{ route('uploadImage') }}" method="post" enctype="multipart/form-data" id="myForm">
        @csrf
        <input type="file" name="images[]" multiple>
        <select name="format">
            @foreach($formats as $format)
                <option value="{{ $format->id }}">{{ ($format->name) }}</option>
            @endforeach
        </select>
        <input type="checkbox" name="resize" value="1" id="resizecheck"> Resize
    </form>
    <button type="button" onclick="document.getElementById('myForm').submit()">Submit</button>
</body>

<script>
    let checkbox = document.getElementById('resizecheck');
    // when checkbox is checked, show the width and height input fields
    checkbox.addEventListener('change', function() {
        if (checkbox.checked) {
            // add html <input type="number" name="width" placeholder="Width px" class="hidden"> Resize and <input type="number" name="height" placeholder="Height px" class="hidden">
            let width = document.createElement('input');
            width.type = 'number';
            width.name = 'width';
            width.placeholder = 'Width px';
            width.classList.add('hidden');
            let height = document.createElement('input');
            height.type = 'number';
            height.name = 'height';
            height.placeholder = 'Height px';
            height.classList.add('hidden');
            document.querySelector('form').appendChild(width);
            document.querySelector('form').appendChild(height);
            width.classList.remove('hidden');
            height.classList.remove('hidden');
        } else {
            // remove the width and height input fields
            let width = document.querySelector('input[name="width"]');
            let height = document.querySelector('input[name="height"]');
            width.classList.add('hidden');
            height.classList.add('hidden');
            width.remove();
            height.remove();
        }
    });
</script>

</html>