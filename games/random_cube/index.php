<game id="game"></game>

<script language="JavaScript">
    (function( $ ){
        let game_field = null;

        let cubes = 1;
        let round = 1;

        let user_score = 0;
        let comp_score = 0;

        function call_end_game(){
            if (end_game_func != null){
                end_game_func(food_eaten);
            }
        }

        $.fn.myGame = function(option, arg1){
            if (option == 'create'){
                game_field = this[0];
                create_field();

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

                $("#slider").slider({
                    step: 1,
                    min: 1,
                    max: 3,
                    slide: function(event, ui) {
                        cubes = ui.value;

                        let cubes_cell = document.getElementById("cubes");
                        cubes_cell.innerHTML = '';
                        for (let c = 0; c < cubes; c++){
                            cubes_cell.innerHTML += `<img style='height:150px; width:200px' src='games/random_cube/cube.png'>`;
                        }
                    },
                });
            }
            else if (option == 'start'){
                start();
            }
            else if (option == 'surrender'){
                surrender();
            }
        };

        function create_field(){
            let game_body = `
                <table align='center'>
                    <tr>
                        <td><p id='user_score' style='font-size:20px' align='center'>Счёт игрока: 0</p></td>
                        <td id='round' style='height:170px; width:200px; font-size:20px' align='center'>Раунд 1</td>
                        <td><p id='computer_score' style='font-size:20px' align='center'>Счёт компьютера: 0</p></td>
                    </tr>
                    <tr>
                        <td id='user_icon'><img style='height:170px; width:200px' src='games/random_cube/user.png'></td>
                        <td id='cubes' align='center' style='height:150px; width:400px'>
                            <img style='height:150px; width:200px' src='games/random_cube/cube.png'>
                        </td>
                        <td id='computer_icon'><img style='height:105px; width:200px' src='games/random_cube/comp.png'></td>
                    </tr>
                    <tr>
                        <td><p style='font-size:24px' align='center'>Игрок</p></td>
                        <td></td>
                        <td><p style='font-size:24px' align='center'>Компьютер</p></td>
                    </tr>
                    <tr>
                        <td id='user_drop'></td>
                        <td align='center'>
                            <p>
                                <button style='background-color:lightblue;border:3px solid blue;height:45px; width:80px' onclick="$('#game').myGame('start')">Бросить кубики</button>
                            </p>
                            <p>
                                <button style='background-color:lightblue;border:3px solid blue;height:45px; width:80px' onclick="document.location.reload();">Начать сначала</button>
                            </p>
                            <button style='background-color:lightblue;border:3px solid blue;height:45px; width:80px' onclick="$('#game').myGame('surrender')"">Сдаться</button>
                            
                            <p>
                                <div>Количество кубиков</div>
                                <p type="text" id="slider" readonly="readonly">
                            </p>

                            
                        </td>
                        <td id='comp_drop'></td>
                    </tr>
                </table>

                <div id="end_dialog" title="Игра окончена">
                    <p id="side"></p>
                </div>
            `;

            game_field.innerHTML = game_body;
        }

        function start(){
            if (round == 7){
                return;
            }
            let round_title = document.getElementById("round");
            round_title.innerHTML = `Раунд ${round}`;

            let user_drop = document.getElementById("user_drop");
            let comp_drop = document.getElementById("comp_drop");

            let user_sum = 0;
            let comp_sum = 0;

            user_drop.innerHTML = '';
            comp_drop.innerHTML = '';
            
            for (let c = 0; c < cubes; c++){
                let number = Math.floor(Math.random() * 6) + 1;
                user_sum += number;
                user_drop.innerHTML += `<p> Бросок ${c+1} - выпало ${number} </p>`;

                number = Math.floor(Math.random() * 6) + 1;
                comp_sum += number;
                comp_drop.innerHTML += `<p> Бросок ${c+1} - выпало ${number} </p>`;
            }

            if (user_sum == comp_sum){
                document.getElementById("user_icon").style.backgroundColor = '#FFAAA1';
                document.getElementById("computer_icon").style.backgroundColor = '#FFAAA1';
            }
            else if (user_sum > comp_sum){
                user_score++;
                document.getElementById("user_score").innerHTML = `Счёт игрока: ${user_score}`;

                document.getElementById("user_icon").style.backgroundColor = '#00FF00';
                document.getElementById("computer_icon").style.backgroundColor = '#FF0000';
            }
            else{
                comp_score++;
                document.getElementById("computer_score").innerHTML = `Счёт компьютера: ${comp_score}`;

                document.getElementById("user_icon").style.backgroundColor = '#FF0000';
                document.getElementById("computer_icon").style.backgroundColor = '#00FF00';
            }

            if (round == 6){
                let side = document.getElementById("side");

                if (user_score == comp_score){
                    side.innerHTML = "Ничья";
                }
                else if (user_score > comp_score){
                    side.innerHTML = "Выиграл игрок";
                }
                else{
                    side.innerHTML = "Выиграл компьютер";
                }

                $("#end_dialog").dialog("open");
            }

            round++;
        }

        function surrender(){
            if (round == 7){
                return;
            }
            let side = document.getElementById("side");

            side.innerHTML = "Выиграл компьютер";

            $("#end_dialog").dialog("open");
        }

    })(jQuery);

    $("#game").myGame('create');
</script>