<canvas id="myCanvas" width="640" height="640">
    <img id='hero' src='games/treasure/hero.png'>
    <img id='treasure' src='games/treasure/treasure.png'>
</canvas>

<div id="end_dialog" title="В поисках сокровищ">
    <p>Игра окончена</p>
    <p id="inner_dialog"></p>
</div>

<script>
    (function( $ ) {
        var canvas = null;
        var ctx = null;
        var heroImage = null;
        var heroImage = null;

        var x = 0;
        var y = 0;
        var dx = 6;
        var dy = -6;
        var heroWidth = 50;
        var heroHeight = 50;

        var treasureX = 0;
        var treasureY = 0;
        var treasureWidth = 50;
        var treasureHeight = 50;

        var jumpPressed = false;
        var jumpState = 0; // 0 - ничего, 1 - наверх, 2 - вниз
        var jumpIter = 20;
        var jumpCounter = 0;
        var rightPressed = false;
        var leftPressed = false;

        var brickHeight = 10;

        var field = [];
        var fieldWidth = 10;
        var fieldHeight = 10;
        var cellWidth = 0;
        var cellHeight = 0;

        var end_game_func = null;

        $.fn.myPlugin = function(given_end_game_func) {
            canvas = this[0];
            ctx = canvas.getContext('2d');
            heroImage = document.getElementById('hero');
            treasureImage = document.getElementById('treasure');

            cellWidth = canvas.width / fieldWidth;
            cellHeight = canvas.height / fieldHeight;
            dy = cellHeight / jumpIter + 2;

            for(var r = 0; r < fieldWidth; r++) {
                var row = [];
                var len = 0;
                for(var c = 0; c < fieldHeight; c++) {
                    var brick = Math.round(Math.random());
                    if (brick){
                        len++;
                    }
                    if (len<fieldWidth){
                        row.push(brick);
                    }
                    else{
                        row.push(0);
                    }
                }
                field.push(row);
            }

            label:for(var r = 0; r < fieldHeight; r++) {
                for(var c = 0; c < fieldWidth; c++) {
                    if (field[r][c]){
                        field[r][c] = 2;
                        treasureX = c * cellWidth + (cellWidth - treasureWidth)/2;
                        treasureY = (r+1) * cellHeight - treasureHeight - brickHeight;
                        break label;
                    }
                }
            }

            label:for(var r = fieldHeight-1; r > 0 ; r--) {
                for(var c = fieldWidth-1; c > 0; c--) {
                    if (field[r][c]){
                        x = c * cellWidth + (cellWidth - heroWidth)/2;
                        y = (r+1) * cellHeight - heroHeight - brickHeight;
                        break label;
                    }
                }
            }

            end_game_func = given_end_game_func;
            document.addEventListener("keydown", keyDownHandler, false);
            document.addEventListener("keyup", keyUpHandler, false);

            draw();
        };

        function keyDownHandler(e) {
            if(e.code  == "ArrowRight") {
                rightPressed = true;
            }
            else if(e.code == 'ArrowLeft') {
                leftPressed = true;
            }
            else if(e.code == 'ArrowUp') {
                jumpPressed = true;
            }
        }
        function keyUpHandler(e) {
            if(e.code == 'ArrowRight') {
                rightPressed = false;
            }
            else if(e.code == 'ArrowLeft') {
                leftPressed = false;
            }
            else if(e.code == 'ArrowUp') {
                jumpPressed = false;
            }
        }

        function drawHero() {
            ctx.drawImage(heroImage, x, y, heroWidth, heroHeight);
        }

        function drawTreasure() {
            ctx.drawImage(treasureImage, treasureX, treasureY, treasureWidth, treasureHeight);
        }

        function drawBricks() {
            for(var r = 0; r < fieldHeight; r++) {
                for(var c = 0; c < fieldWidth; c++) {
                    if (field[r][c]){
                        var brickX = c * cellWidth;
                        var brickY = (r+1) * cellHeight - brickHeight;
                    }

                    ctx.fillStyle = '#FFCBDB';
                    ctx.fillRect(brickX, brickY, cellWidth, brickHeight);
                }
            }
        }

        function draw() {
            if(rightPressed) {
                x += dx;
                if (x > canvas.width - heroWidth){
                    x = canvas.width - heroWidth;
                }
            }
            else if(leftPressed) {
                x -= dx;
                if (x < 0){
                    x = 0;
                }
            }

            if (jumpState == 0){
                if (jumpPressed){
                    jumpState = 1;
                }
            }
            else if (jumpState == 1){
                jumpCounter++;
                if (jumpCounter == jumpIter){
                    jumpState = 2;
                    jumpCounter = 0;
                }
                y -= dy;
            }
            else{
                y += dy;
            }

            if (y + heroHeight >= canvas.height){
                end_game_func(false);
                return;
            }
            else if (y < 0){
                y = 0;
            }

            var row = Math.floor((y + heroHeight) / cellHeight);
            var col =  Math.floor((x + heroWidth / 2) / cellWidth);

            if (field[row][col]){
                if (field[row][col]==2){
                    end_game_func(true);
                    return;
                }

                if (y + heroHeight > (row + 1) * cellHeight - brickHeight){
                    y = (row + 1) * cellHeight - brickHeight - heroHeight;

                    if (jumpState != 1){
                        jumpState = 0;
                    }
                }
            }
            else{
                if (jumpState != 1){
                    jumpState = 2;
                }
            }

            ctx.fillStyle = '#000';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            drawBricks();
            drawTreasure();
            drawHero();

            window.requestAnimationFrame(draw);
        }
    })(jQuery);

    $("#myCanvas").myPlugin(end_game);

    $(function(){
        $("#end_dialog").dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                "Начать сначала": function() {
                    $(this).dialog("close");
                    document.location.reload();
                }
            }
        });
    });

    function end_game(result){
        if (result){
            document.getElementById("inner_dialog").innerHTML += "Вы выиграли";
        }
        else{
            document.getElementById("inner_dialog").innerHTML += "Вы проиграли";
        }
        $("#end_dialog").dialog("open");
    }
</script>