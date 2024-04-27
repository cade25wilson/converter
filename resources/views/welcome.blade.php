<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        <title>File Converter</title>
        <meta name="description" content="Welcome to our file converter website. We provide fast, reliable and easy-to-use solutions for converting files between various formats.">
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
