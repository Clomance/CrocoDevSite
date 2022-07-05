<html>
    <head>
        <meta charset = "utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style><?php include_once("main_styles.css");?></style>
        <style><?php include_once("games/find_a_card_pair/style.css");?></style>
    </head>

    <body>
        <?php include("navigation_bar.html"); ?>

        <div class="Page">
            <div>
                <div class="GameName">Найди пару</div>
                <!-- <div class="RetryButton"></div> -->
            </div>

            <div id="GameSection"></div>

            <div class="action_button_container">
                <button class="action_button" onclick="action(this)">Начать</button>
            </div>

            <!-- <div id="DialogueBox" class="DialogueBox non-display">
                Игра окончена
                <button onclick="this.classList.add('non-display');">Ок</button>
            </div> -->

            <script>
                // Размеры карточного поля
                let fwidth = 8;
                let fheight= 8;
                let flength = 64;

                let game_state = 0;

                let selected = false;
                let selected_id = 0;

                set_up_field();

                function index(c, d){
                    return c * fwidth + d;
                }

                function set_up_field(){
                    let game_board = document.getElementById('GameSection');

                    let table = document.createElement('div');
                    table.className = "card_container";

                    for (let i = 0; i < fheight; i++){
                        let row = document.createElement('div');
                        row.className = "row";

                        for (let d = 0; d < fwidth; d++){
                            let card_wrapper = document.createElement('div');
                            card_wrapper.classList = "card_wrapper";

                            let card_base = document.createElement('div');
                            card_base.classList = "card_base";
                            card_base.onclick = function(){
                                open_card(index(i, d));
                            };
                            card_base.id = "card_base_" + index(i, d);

                            let card_content = document.createElement('div');
                            card_content.classList = "card_content";

                            let card_text = document.createElement('div');
                            card_text.classList = "card_text";
                            card_text.id = "card_text_" + index(i, d);

                            card_content.appendChild(card_text);

                            card_base.appendChild(card_content);

                            card_wrapper.appendChild(card_base);

                            row.appendChild(card_wrapper);
                        }
                        table.appendChild(row);
                    }

                    game_board.appendChild(table);
                }

                function set_card(){
                    let ids = [];

                    for (let i = 0; i < flength; i++){
                        ids.push(i);
                    }

                    for (let i = 0; i < flength; i+=2){
                        let num = Math.floor(Math.random() * flength);

                        let array_id = Math.floor(Math.random() * ids.length);

                        let id = ids.splice(array_id, 1)[0];

                        document.getElementById("card_text_" + id).innerHTML = num;

                        array_id = Math.floor(Math.random() * ids.length);
                        let id2 = ids.splice(array_id, 1)[0];

                        document.getElementById("card_text_" + id2).innerHTML = num;
                    }
                }

                function action(button){
                    switch (game_state){
                        case 0:
                            game_state = 2;

                            button.style.backgroundColor = "rgb(120, 120, 120)";

                            button.innerHTML = "Перевернуть";

                            set_card();

                            break;

                        case 1:
                            button.innerHTML = "Перевернуть";

                            clear();
                            set_card();

                            game_state++;
                            break;

                        case 2:
                            button.innerHTML = "Заново";

                            hide_cards();

                            game_state--;
                            break;
                    }
                }

                function clear(){
                    for (let i = 0; i < flength; i++){
                        let card_base_id = "card_base_" + i;
                        let card_base = document.getElementById(card_base_id);
                        card_base.className = "card_base";

                        let card_text_id = "card_text_" + i;
                        let card_text = document.getElementById(card_text_id);
                        card_text.classList.remove("hidden");
                    }
                }

                function hide_cards(){
                    for (let i = 0; i < flength; i++){
                        let card_text_id = "card_text_" + i;
                        let card_text = document.getElementById(card_text_id);
                        card_text.classList.add("hidden");
                    }
                }

                function open_card(id){
                    let card_base_id = "card_base_" + id;
                    let card_text_id = "card_text_" + id;

                    let card_base = document.getElementById(card_base_id);
                    let card_text = document.getElementById(card_text_id);

                    if (game_state == 1){
                        if (!card_text.classList.contains("hidden")){
                            return;
                        }

                        if (selected_id === id){
                            return;
                        }

                        card_text.classList.remove("hidden");

                        if (selected){
                            selected = false;

                            let card_base_id2 = "card_base_" + selected_id;
                            let card_base2 = document.getElementById(card_base_id2);

                            let card_text_id2 = "card_text_" + selected_id;
                            let card_text2 = document.getElementById(card_text_id2);

                            if (card_text.innerHTML === card_text2.innerHTML){
                                card_base.classList.add("matched");
                                card_base2.classList.add("matched");
                            }
                            else{
                                card_base.classList.add("not_matched");
                                card_base2.classList.add("not_matched");
                            }
                        }
                        else{
                            card_base.classList.add("selected");

                            selected_id = id;

                            selected = true;
                        }
                    }
                }
            </script>
        </div>
    </body>
</html>