<style>
    #game_field td {
        border: 1px solid white;
        width: 30px;
        height: 30px;
        background-color:#000;
    }

    table button {
        border: 1px solid white;
        width: 80px;
        height: 80px;
        font-size: 40px;
    }
</style>
<game_field align="center" id="game_field"></game_field>

<table align="center" style="margin-top:10px; display: none;">
    <tr>
        <td></td>
        <td>
            <button onclick=>↑</button>
        </td>
        <td></td>
    </tr>
    <tr>
        <td><button>←</button></td>
        <td><button>↓</button></td>
        <td><button>→</button></td>
    </tr>
</table>

<div id="start_dialog" title="Тетрис">
    <p>Управление</p>
    <p>Стрелочка вправо - движение вправо</p>
    <p>Стрелочка влево - движение вправо</p>
    <p>Стрелочка вверх - поворот</p>
    <p>Стрелочка вниз - движение вниз</p>
</div>

<div id="end_dialog" title="Тетрис">
    <p>Игра окончена</p>
</div>

<script language="JavaScript">
    (function( $ ){
        class Point{
            constructor(x, y){
                this.x = x;
                this.y = y;
            }

            copy(){
                let point = new Point(this.x, this.y);
                return point;
            }

            move(point){
                this.x = point.x;
                this.y = point.y;
            }

            draw(part){
                let id = this.x + this.y * field_width;
                let game_field_cell = document.getElementById(id);
                if (game_field_cell !=  null){
                    game_field_cell.style.backgroundColor = part;
                }
            }

            field_id(){
                return this.x + this.y * field_width;
            }
        }

        let game_field = null;

        let logic_field = [];
        let field_width = 10;
        let field_height = 18;

        let audioElement = null;
        let intervalId = 0;

        let score = 0;

        let object = [new Point(0,0),new Point(0,0),new Point(0,0),new Point(0,0)];
        let tail_id = 0;
        let tail = [new Point(0,0),new Point(0,0),new Point(0,0)];
        let rotate = 0;

        let tails = [
            [new Point(0,1),new Point(-1,0),new Point(1,0)],
            [new Point(0,-1),new Point(0,-2),new Point(0,-3)],
            [new Point(-1,0),new Point(-1,-1),new Point(0,-1)],
        ]

        let end_game_func;
        let called = false;

        function call_end_game(){
            document.onkeydown = null;
            clearInterval(intervalId);
            if (end_game_func != null && !called){
                called = true;
                end_game_func(score);
            }
        }

        $.fn.tetris = function(func){
            audioElement = document.createElement('audio');
            audioElement.setAttribute('src', 'games/tetris/Tetris.mp3');
            audioElement.play();
            audioElement.addEventListener('ended', function() {
                this.play();
            }, false);

            game_field = this[0];
            end_game_func = func;

            gen_object();
            create_field();

            document.onkeydown = on_click;

            create_update_event();

            // Установка деструктора (удаляет музыку, события обновления и нажатия кнопок)
            set_game_drop(function(){
                document.onkeydown = null;
                audioElement.src = ""
                clearInterval(intervalId);
            });
        };

        function create_field(){
            let tbdy = document.createElement('tbody');
            for (let i = 0; i < field_height; i++){
                let row = [];
                let tr = document.createElement('tr');
                for (let d = 0; d < field_width; d++){
                    row.push(0);
                    let td = document.createElement('td');
                    td.id = i * field_width + d;
                    tr.appendChild(td);
                }
                logic_field.push(row);
                tbdy.appendChild(tr);
            }
            game_field.appendChild(tbdy);

            for (let f = 0; f < 4; f++){
                object[f].draw("#00FF00");
            }
        }

        function create_update_event(){
            intervalId = setInterval(move, 500);
        }

        function gen_object(){
            object[0].x = field_width/2;
            object[0].y = 0;

            tail_id = Math.floor(Math.random() * tails.length);
            for (let i = 0; i < 3; i++){
                tail.push(tails[tail_id][i]);
            }

            for (let i = 1; i < 4; i ++){
                object[i] = new Point(object[0].x + tails[tail_id][i-1].x, object[0].y + tails[tail_id][i-1].y);
            }
        }

        function clear_row(row){
            for (let i = row; i > 0; i--){
                for (let d = 0; d < field_width; d++){
                    logic_field[i][d] = logic_field[i-1][d];
                }
            }

            for (let d = 0; d < field_width; d++){
                    logic_field[0][d] = 0;
                }

            for (let i = 0; i < field_height; i++){
                for (let d = 0; d < field_width; d++){
                    let id = d + i * field_width;
                    let game_field_cell = document.getElementById(id);
                    if (logic_field[i][d]){
                        game_field_cell.style.backgroundColor = '#AAA';
                    }
                    else{
                        game_field_cell.style.backgroundColor = '#000';
                    }
                }
            }
        }

        function kill_object(){
            rotate = 0;
            for (let f = 0; f < 4; f++){
                object[f].draw("#AAA");
                if (logic_field[object[f].y] != null){
                    logic_field[object[f].y][object[f].x] = 1;
                }
                else{
                    call_end_game();
                    return;
                }
            }

            for (let i = 0; i < field_height; i++){
                let c = 0;
                for (let d = 0; d < field_width; d++){
                    if (!logic_field[i][d]){
                        break;
                    }
                    c++;
                }
                if (c == field_width){
                    clear_row(i);
                }
            }
        }

        function move(){
            for (let i = 0; i < 4; i++){
                object[i].draw("#000");
                object[i].y += 1;
            }

            if (!check_object_ver()){
                for (let i = 0; i < 4; i++){
                    object[i].y -= 1;
                }

                kill_object();

                gen_object();
            }
            else{
                for (let i = 0; i < 4; i++){
                    object[i].draw("#00FF00");
                }
            }
        }

        function on_click(e){
            if (e.which == 40){ // down
                clearInterval(intervalId);
                move();
                intervalId = setInterval(move, 500);

                e.preventDefault();
            }
            else if (e.which == 37 || e.which == 38 || e.which == 39){
                for (let i = 0; i < 4; i++){
                    object[i].draw("#000");
                }

                if (e.which == 39){ // right
                    for (let i = 0; i < 4; i++){
                        object[i].x += 1;
                    }

                    if (!check_object_hor()){
                        for (let i = 0; i < 4; i++){
                            object[i].x -= 1;
                        }
                    }
                }
                else if (e.which == 37){ // left
                    for (let i = 0; i < 4; i++){
                        object[i].x -= 1;
                    }

                    if (!check_object_hor()){
                        for (let i = 0; i < 4; i++){
                            object[i].x += 1;
                        }
                    }
                }
                else if (e.which == 38){ // up
                    let last_rotate = rotate;
                    let last_tail = [];
                    for (let i = 0; i < 3; i++){
                        last_tail.push(tail[i]);
                    }

                    switch(rotate){
                        case 0:
                            for (let i = 0; i < 3; i++){
                                tail[i] = new Point(tails[tail_id][i].y, tails[tail_id][i].x);
                            }
                            rotate=1;
                            break;
                        case 1:
                            for (let i = 0; i < 3; i++){
                                tail[i] = new Point(tails[tail_id][i].x, -tails[tail_id][i].y);
                            }
                            rotate=2;
                            break;
                        case 2:
                            for (let i = 0; i < 3; i++){
                                tail[i] = new Point(-tails[tail_id][i].y, -tails[tail_id][i].x);
                            }
                            rotate=3;
                            break;
                        case 3:
                            tail = tails[tail_id];
                            rotate = 0;
                            break;
                    }

                    for (let i = 1; i < 4; i++){
                        object[i] = new Point(object[0].x + tail[i-1].x, object[0].y + tail[i-1].y);
                    }

                    if (!check_object_hor() || !check_object_ver()){
                        for (let i = 1; i < 4; i++){
                            object[i] = new Point(object[0].x + last_tail[i-1].x, object[0].y + last_tail[i-1].y);
                        }
                        rotate = last_rotate;
                    }
                }

                for (let i = 0; i < 4; i++){
                    object[i].draw("#00FF00");
                }

                e.preventDefault();
            }
        }

        function check_object_hor(){
            for (let i = 0; i < 4; i++){
                let lfr = logic_field[object[i].y];
                if (object[i].x < 0 || object[i].x >= field_width || (lfr != null && logic_field[object[i].y][object[i].x])){
                    return false;
                }
            }

            return true;
        }

        function check_object_ver(){
            for (let i = 0; i < 4; i++){
                let lfr = logic_field[object[i].y];
                if (object[i].y == field_height || (lfr != null && logic_field[object[i].y][object[i].x])){
                    return false;
                }
            }

            return true;
        }

    })(jQuery);

    $("#start_dialog").dialog({
        autoOpen: true,
        modal: true,
        buttons: {
            "Начать": function() {
                $("#game_field").tetris(end_game);

                $(this).dialog("close");
            }
        }
    });

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

    function end_game(food_eaten){
        $("#end_dialog").dialog("open");
    }
</script>