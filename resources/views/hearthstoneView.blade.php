<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>hearthstoneView</title>
</head>
<body>
    <form method="POST" action="/form-submit">
        @csrf
        <input name="sliderTest" type="range" min="{{$min}}" max="{{$max}}" value="{{($max+$min)/2}}" id="slider">
        <button type="submit">Submit: <span id="sliderValue">50</span></button>
    </form>

    <script>
        slider = document.getElementById("slider");
        output = document.getElementById("sliderValue");
        output.innerHTML = slider.value;
        slider.oninput = function() {
            output.innerHTML = this.value;
        };
    </script>
</body>
</html>
