<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Opening App...</title>
</head>
<body>
    <p>Opening App, please wait...</p>

    <script type="text/javascript">
        window.location.href = "{!! $deepLink !!}";
    </script>

    <noscript>
        <a href="{!! $deepLink !!}">Open App</a>
    </noscript>
</body>
</html>
