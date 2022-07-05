<style>
    td {
        border: 0px solid black;
        width: 20px;
        height: 20px;
        background-color:#FFF;
    }
</style>
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
    (function( $ ){
        let game_field = null;

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
                game_field_cell.innerHTML = part;
            }

            field_id(){
                return this.x + this.y * field_width;
            }
        }

        let field_width = 18;
        let field_height = 18;

        let intervalId = 0;
        let food = [];
        let food_eaten = 0;

        let snake = [new Point(3, 0), new Point(2, 0), new Point(1, 0), new Point(0, 0)];

        // 0 - up, 1 - left, 2 - down, 3 - right
        let direction = 3;

        let end_game_func;

        function call_end_game(){
            clearInterval(intervalId);
            if (end_game_func != null){
                end_game_func(food_eaten);
            }
        }

        $.fn.snake = function(amount_of_food, func){
            game_field = this[0];
            end_game_func = func;

            document.onkeydown = function(e){
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
            };

            create_field();
            set_food(amount_of_food);
            create_update_event();

            set_game_drop(function(){
                document.onkeydown = null;
                clearInterval(intervalId);
            });
        };

        function create_field(){
            let tbdy = document.createElement('tbody');
            for (let i = 0; i < field_height; i++){
                let tr = document.createElement('tr');
                for (let d = 0; d < field_width; d++){
                    let td = document.createElement('td');
                    td.id = i * field_width + d;
                    tr.appendChild(td);
                }
                tbdy.appendChild(tr);
            }
            game_field.appendChild(tbdy);

            for (let f = 0; f < snake.length; f++){
                let cell = document.getElementById(snake[f].field_id());
                cell.style.backgroundColor = "#00FF00";
            }
        }

        function create_update_event(){
            intervalId = setInterval(move, 500);
        }

        function set_food(amount){
            label1:for (let i = 0; i < amount; ){
                let x = Math.floor(Math.random() * field_width);
                let y = Math.floor(Math.random() * field_height);

                for (let f = 0; f < snake.length; f++){
                    if (y == snake[f].y && (x == snake[f].x || x == snake[f].x+1)){
                        continue label1;
                    }
                }

                for (let f = 0; f < food.length; f++){
                    if (x == food[f].x && y == food[f].y){
                        continue label1;
                    }
                }

                let point = new Point(x, y);
                let cell = document.getElementById(point.field_id());
                cell.style.backgroundColor = "#FF0000";
                food.push(point);
                i++;
            }
        }

        function move(){
            let next_head_pos = snake[0].copy();
            let last_tail_pos = snake[snake.length-1].copy();

            switch (direction){
                case 0: // up
                    if (next_head_pos.y == 0){
                        call_end_game();
                        return;
                    }
                    next_head_pos.y -= 1;
                    break;
                case 1: // left
                    if (next_head_pos.x == 0){
                        call_end_game();
                        return;
                    }
                    next_head_pos.x -= 1;
                    break;
                case 2: // down
                    if (next_head_pos.y == field_height){
                        call_end_game();
                        return;
                    }
                    next_head_pos.y += 1;
                    break;
                case 3: // right
                    if (next_head_pos.x == field_width){
                        call_end_game();
                        return;
                    }
                    next_head_pos.x += 1;
                    break;
            }

            let next_head_cell = document.getElementById(next_head_pos.field_id());

            if (next_head_cell.style.backgroundColor == "rgb(255, 0, 0)"){
                eat_food(next_head_pos);
                snake.unshift(next_head_pos);
            }
            else if (next_head_cell.style.backgroundColor == "rgb(0, 255, 0)"){
                call_end_game();
            }
            else{
                for (let i = snake.length-1; i > 0; i--){
                    snake[i].move(snake[i-1]);
                }
                let last_tail_cell = document.getElementById(last_tail_pos.field_id());
                last_tail_cell.style.backgroundColor = "#FFFFFF";

                let tail_cell = document.getElementById(snake[snake.length-1].field_id());
                tail_cell.style.backgroundColor = "#00FF00";

                snake[0].move(next_head_pos);
            }

            next_head_cell.style.backgroundColor = "#00FF00";
        }

        function eat_food(point){
            for (let i=0; i < food.length; i++){
                if (point.x == food[i].x && point.y == food[i].y){
                    food.splice(i, 1);
                    food_eaten++;
                    if (food.length == 0){
                        call_end_game();
                    }
                    return;
                }
            }
        }
    })(jQuery);

    $("#start_dialog").dialog({
        autoOpen: true,
        modal: true,
        buttons: {
            "Старт": function() {
                let food_amount = parseInt(document.getElementById("food_amount").value, "10");
                if (isNaN(food_amount) || food_amount<1 || food_amount>100){
                    return;
                }

                $("#game_field").snake(food_amount, end_game);

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
        let score = document.getElementById("score");
        score.innerHTML += food_eaten;
        $("#end_dialog").dialog("open");
    }
</script>