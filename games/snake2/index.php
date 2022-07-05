<style type="text/css">
    th, td {
        border: 1px solid black;
        width: 50px;
        height: 50px;
        overflow: hidden;
        background-color:#FFF;
    }
</style>

<h1>Змейка</h1>

<game_field id="game_field"></game_field>

<div id="start_dialog" title="Змейка">
    <p>Управление: стрелочки клавиатуры</p>
    <p>Введите количество еды на поле (от 1 до 100)</p>
    <p><input id="food_amount" type="text"></p>
</div>

<div id="end_dialog" title="Змейка">
    <p>Игра окончена</p>
    <p id="score">Ваш счёт: </p>
</div>

<script language="JavaScript">
    {
        class Point{
            constructor(x, y){
                this.x = x;
                this.y = y;
            }

            copy(){
                var point = new Point(this.x, this.y);
                return point;
            }

            shift(dx, dy){
                this.x += dx;
                this.y += dy;
            }

            shifted_copy(dx, dy){
                var point = new Point(this.x, this.y);
                point.shift(dx, dy);
                return point;
            }

            move(point){
                this.x = point.x;
                this.y = point.y;
            }

            draw(part){
                var id = this.x + this.y * field_width;
                var game_field_cell = document.getElementById("game_field_cell_" + id);
                game_field_cell.innerHTML = part;
                game_field_cell.style = "font-size:40px";
            }

            field_id(){
                return this.x + this.y * 15;
            }
        }

        var field_width = 15;
        var field_height = 15;

        var intervalId = 0;
        var food = [];
        var food_eaten = 0;

        var head_pos = new Point(3, 0);
        var neck_pos = new Point(2, 0);
        var body = [new Point(1, 0)];
        var tail_pos = new Point(0, 0);

        // 0 - up, 1 - left, 2 - down, 3 - right
        var direction = 3;

        $("#start_dialog").dialog({
            autoOpen: true,
            modal: true,
            buttons: {
                "Старт": function() {
                    var food_amount = parseInt(document.getElementById("food_amount").value, "10");
                    if (isNaN(food_amount) || food_amount<1 || food_amount>100){
                        return;
                    }

                    field_width = 15;
                    field_height = 15;

                    intervalId = 0;
                    food = [];
                    food_eaten = 0;

                    head_pos = new Point(3, 0);
                    neck_pos = new Point(2, 0);
                    body = [new Point(1, 0)];
                    tail_pos = new Point(0, 0);

                    // 0 - up, 1 - left, 2 - down, 3 - right
                    direction = 3;

                    $(this).dialog("close");
                    create_game_field();
                    set_food(food_amount);
                    create_update_event();

                    set_game_drop(function(){
                        document.onkeydown = null;
                        clearInterval(intervalId);
                    });
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

        function clear_field(){
            for (var i=0;i<field_height;i++){
                for (var d=0;d<field_width;d++){
                    var id = d + i * field_width;
                    var game_field_cell = document.getElementById("game_field_cell_" + id);
                    game_field_cell.innerHTML = "";
                }
            }

            food.forEach(function(v, i, a){
                v.draw("Еда");
                var game_field_cell = document.getElementById("game_field_cell_" + v.field_id());
                game_field_cell.style = "font-size:20px";
            });
        }

        function set_food(amount){
            label1:for (var i = 0; i < amount; ){
                var x = Math.floor(Math.random() * field_width);
                var y = Math.floor(Math.random() * field_height);

                if (
                    x == head_pos.x+1 && y == head_pos.y ||
                    x == head_pos.x && y == head_pos.y ||
                    x == neck_pos.x && y == neck_pos.y ||
                    x == body[0].x && y == body[0].y ||
                    x == tail_pos.x && y == tail_pos.y
                ){
                    continue label1;
                }

                for (var f = 0; f < food.length; f++){
                    if (x == food[f].x && y == food[f].y){
                        continue label1;
                    }
                }

                var point = new Point(x, y);
                food.push(point);
                i++;
            }
        }

        function end_game(){
            clearInterval(intervalId);
            var score = document.getElementById("score");
            score.innerHTML += food_eaten;
            $("#end_dialog").dialog("open");
        }

        function move(){
            var next_head_pos = head_pos.copy();
            switch (direction){
                case 0: // up
                    next_head_pos.shift(0, -1);
                    if (next_head_pos.y==-1){
                        end_game();
                        return;
                    }

                    break;
                case 1: // left
                    next_head_pos.shift(-1, 0);
                    if (next_head_pos.x==-1){
                        end_game();
                        return;
                    }
                    break;
                case 2: // down
                    next_head_pos.shift(0, 1);
                    if (next_head_pos.y==field_height){
                        end_game();
                        return;
                    }
                    break;
                case 3: // right
                    next_head_pos.shift(1, 0);
                    if (next_head_pos.x==field_width){
                        end_game();
                        return;
                    }
                    break;
            }

            var game_field_cell = document.getElementById("game_field_cell_" + next_head_pos.field_id());
            if (game_field_cell.innerHTML=="Еда"){
                eat_food(next_head_pos);
                body.unshift(neck_pos.copy());
            }
            else{
                tail_pos.move(body[body.length-1]);

                for (var i = body.length-1; i>0; i--){
                    body[i].move(body[i-1]);
                }
                body[0].move(neck_pos);
            }

            neck_pos.move(head_pos);

            switch (direction){
                case 0: // up
                    head_pos.shift(0, -1);
                    break;
                case 1: // left
                    head_pos.shift(-1, 0);
                    break;
                case 2: // down
                    head_pos.shift(0, 1);
                    break;
                case 3: // right
                    head_pos.shift(1, 0);
                    break;
            }
        }

        function create_game_field(){
            var game_field = document.getElementById("game_field");

            var tbdy = document.createElement('tbody');

            for (var i=0;i<field_height;i++){
                var tr = document.createElement('tr');

                for (var d=0;d<field_width;d++){
                    var td = document.createElement('td');

                    td.id = "game_field_cell_" + (i*field_width + d);
                    td.style = "font-size:40px;";
                    td.align = "center";

                    tr.appendChild(td);
                }

                tbdy.appendChild(tr);
            }

            game_field.appendChild(tbdy);
        }

        function create_update_event(){
            intervalId = setInterval(function(){
                    move();
                    clear_field();
                    head_pos.draw("З");
                    neck_pos.draw("м");
                    body.forEach(function(v,i,a){
                        v.draw("е");
                    });
                    tail_pos.draw("я");
                },
                1000
            );
        }

        document.onkeydown = function(e){
            var window_width = window.innerWidth, window_height = window.innerHeight;

            switch(e.which) {
                case 37: // left
                    direction = 1;
                break;

                case 38: // up
                    direction = 0;
                break;

                case 39: // right
                    direction = 3;
                break;

                case 40: // down
                    direction = 2;
                break;

                default: return; // exit this handler for other keys
            }

            e.preventDefault();
        }

        function eat_food(point){
            for (var i=0; i < food.length; i++){
                if (point.x == food[i].x && point.y == food[i].y){
                    food.splice(i, 1);
                    food_eaten++;
                    if (food.length==0){
                        end_game();
                    }
                    return;
                }
            }
        }
    }
</script>