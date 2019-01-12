<!DOCTYPE HTML>
<html><head>
    <title>Młynek</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html,body{width:100%;height:100%;padding:0;margin:0;}
        #board{width:700px;height:700px;background-color: darkgray;padding:0;margin: 50px}
        #loader{width:460px;height:100px;padding:5px;position:relative;left:900px;border:2px solid black;}
        div.field{width:98px;height:98px;display:inline-flex;border:1px solid black;background-color:blue;flex-direction:row;margin:0;padding:0;}
        div.clickable{background-color:red;}
        div.highlight{background-color:#F99;}
        div.bullet{width:90px;height:90px;border:1px solid black;position:absolute;}
    </style>
    <script src="script/jquery-3.3.1.min.js"></script>
    <script>
        var id = 0;
        var hand = 5;
        function updatePlayer(id){
            $.ajax({
                method: 'POST',
                url: 'ajax.php',
                data: {'function': 'update','id': id},
                success: function(res){
                    showId(res);
                }
            });
        }

        function showId(json){
            if(json != "[]"){
                json = JSON.parse(json);
                id = json['id'];
            }
        }

        window.onload = function () {
            setInterval(function(){updatePlayer(id);}, 1000);
        };

        window.onbeforeunload = function(){
            $.ajax({
                method: 'POST',
                url: 'ajax.php',
                data: {'function': 'disconnect','id': id}
            });
        };

    </script>
</head>
<body>
<div id="board">
</div>
<div id="loader"></div>
<script src="script/genDivs.js"></script>
<script src="script/clickEvents.js"></script>
</body>
</html>