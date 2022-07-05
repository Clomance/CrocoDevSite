<h1>Поиск котиков</h1>

<game_field id="game_field"></game_field>

<div id="start_dialog" title="Поиск котиков">
    <p>В этой игре вам требуется найти спрятаных котиков</p>
    <p>Введите количество спрятаных котиков (от 1 до 100)</p>
    <p><input id="amount_of_cats" type="text"></p>
</div>

<div id="end_dialog" title="Поиск котиков">
    <p>Игра окончена</p>
    <p id="score"></p>
</div>

<script language="JavaScript">
    (function( $ ){
        let field_width = 10;
        let field_height = 10;

        let score = 0;
        let lives = 3;
        let cats = [];

        let end_game_func;

        $.fn.catGame = function(option, arg1, arg2) {
            if (option == "create"){
                game_field = this[0];
                end_game_func = arg2;

                create_game_field();
                set_cats(arg1);
            }
            else if (option == "restart"){
                game_field.innerHTML = "";
                create_game_field();
                set_cats(arg1);
                score = 0;
                lives = 3;
            }
            else if (option == "open_cover"){
                open_cover(arg1);
            }
        };

        function set_cats(amount){
            label1:for (let i = 0; i < amount; ){
                let id = Math.floor(Math.random() * field_width * field_height);

                for (let f = 0; f < cats.length; f++){
                    if (id == cats[f]){
                        continue label1;
                    }
                }

                cats.push(id);

                i++;
            }
        }

        function create_game_field(){
            let tbdy = document.createElement('tbody');

            for (let i=0;i<field_height;i++){
                let tr = document.createElement('tr');

                for (let d=0;d<field_width;d++){
                    let td = document.createElement('td');

                    let id = i*field_width + d;
                    td.id = "game_field_cell_" + id;
                    td.align = "center";
                    td.style = "border:1px solid black;width:50px;height:50px;overflow:hidden;background-color:#FFF;";
                    td.innerHTML = `<img src='games/find_a_cat/cover.jpg' style='width: 100px;height: 100px;' onclick="$(document).catGame('open_cover',${id});">`

                    tr.appendChild(td);
                }

                tbdy.appendChild(tr);
            }

            game_field.appendChild(tbdy);
        }

        function open_cover(id){
            let cell = document.getElementById("game_field_cell_" + id);

            for (let i=0; i < cats.length; i++){
                if (id == cats[i]){
                    cats.splice(i, 1);
                    score++;
                    cell.innerHTML = "<img src='games/find_a_cat/cat.png' style='width: 100px;height: 100px;'>";
                    if (cats.length==0){
                        if (end_game_func != null){
                            end_game_func(score);
                        }
                    }
                    return;
                }
            }

            lives-=1;
            cell.innerHTML = "";

            if (lives==0){
                if (end_game_func != null){
                    end_game_func(score);
                }
            }
        }
    })(jQuery);

    $("#start_dialog").dialog({
        autoOpen: true,
        modal: true,
        buttons: {
            "Старт": function() {
                let amount_of_cats = parseInt(document.getElementById("amount_of_cats").value, "10");
                if (isNaN(amount_of_cats) || amount_of_cats<1 || amount_of_cats>100){
                    return;
                }

                $(this).dialog("close");

                $("#game_field").catGame('create', amount_of_cats, end_game);
            }
        }
    });

    $("#end_dialog").dialog({
        autoOpen: false,
        modal: true,
        buttons: {
            "Начать сначала": function() {
                $(this).dialog("close");

                $("#game_field").catGame('restart');
            }
        }
    });

    function end_game(score){
        let score_dialog = document.getElementById("score");
        score_dialog.innerHTML = "Ваш счёт: " + score;
        $("#end_dialog").dialog("open");
    }
</script>