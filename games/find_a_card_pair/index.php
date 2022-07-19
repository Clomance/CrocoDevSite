<html>
    <head>
        <meta charset = "utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style><?php include_once("main_styles.css");?></style>
        <style><?php include_once("general_styles.css");?></style>
        <style><?php include_once("games/find_a_card_pair/style.css");?></style>
        <style><?php include_once("games/find_a_card_pair/slider_style.css");?></style>
        <style>
            .arrow {
                margin-left:  3px;
                margin-right:  3px;
                border: solid black;
                border-width: 0 3px 3px 0;
                display: inline-block;
                padding: 3px;
            }

            .down {
                transform: rotate(45deg);
                -webkit-transform: rotate(45deg);
            }

            .up {
                transform: rotate(-135deg);
                -webkit-transform: rotate(-135deg);
            }
        </style>
    </head>

    <body>
        <?php include("navigation_bar.html"); ?>

        <div class="Page">
            <div class="GameTitleBar">
                <div class="GameName">Запомни и найди</div>
                <svg id="RetryButton" class="RetryButton hidden" onclick="retry();" viewBox="0 0 24 24">
                    <path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
                </svg>
            </div>

            <div class="SettingsHeader" onclick="switchSettings();">
                <li class="text">Настройки <i id="SettingsHeaderArrow" class="arrow down"></i></li>
            </div>

            <div id="Settings" class="Settings non-display">
                <div class="TimerSettings">
                    <div>Время на запоминание</div>
                    <input id="TimerInput" class="styled-slider slider-progress" type="range" min="10" max="90" step="10" value="10">
                </div>
            </div>

            <div id="Timer" class="Timer hidden">10 сек</div>

            <div class="Game">
                <div id="GameSection"></div>
                <div id="ActionButtonContainer" class="ActionButtonContainer">
                    <button id="ActionButton" class="action_button" onclick="action(this)">Начать</button>
                </div>
            </div>

            <script>
                // Размеры карточного поля
                let fwidth = 8;
                let fheight= 8;
                let flength = 64;

                let game_state = 0;

                let time_left;

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
                    setTimer();

                    ActionButtonContainer.classList.add("non-display");

                    set_card();

                    show_card();
                }

                function retry(){
                    RetryButton.classList.add("hidden");
                    ActionButtonContainer.classList.remove("non-display");

                    clear();

                    game_state = 0;
                }

                function setTimer(){
                    time_left = parseInt(TimerInput.value);

                    Timer.innerHTML = time_left + " сек.";

                    Timer.classList.remove("hidden");

                    let id = setInterval(
                        () => {
                            time_left--;

                            if (time_left == 0){
                                clearInterval(id);

                                Timer.classList.add("hidden");
                                RetryButton.classList.remove("hidden");
                                ActionButtonContainer.classList.add("non-display");

                                hide_cards();

                                game_state = 1;
                            }

                            Timer.innerHTML = time_left + " сек.";
                        },
                        1000
                    );
                }

                function clear(){
                    selected = false;

                    for (let i = 0; i < flength; i++){
                        let card_base_id = "card_base_" + i;
                        let card_base = document.getElementById(card_base_id);
                        card_base.className = "card_base";

                        let card_text_id = "card_text_" + i;
                        let card_text = document.getElementById(card_text_id);

                        if (!card_text.classList.contains("hidden")){
                            card_text.classList.add("hidden");
                        }
                    }
                }

                function hide_cards(){
                    for (let i = 0; i < flength; i++){
                        let card_text_id = "card_text_" + i;
                        let card_text = document.getElementById(card_text_id);
                        card_text.classList.add("hidden");
                    }
                }

                function show_card(){
                    for (let i = 0; i < flength; i++){
                        let card_text_id = "card_text_" + i;
                        let card_text = document.getElementById(card_text_id);
                        card_text.classList.remove("hidden");
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

                function switchSettings(){
                    if (Settings.classList.contains("non-display")){
                        Settings.classList.remove("non-display");

                        SettingsHeaderArrow.classList.remove("down");
                        SettingsHeaderArrow.classList.add("up");
                    }
                    else{
                        Settings.classList.add("non-display");

                        SettingsHeaderArrow.classList.remove("up");
                        SettingsHeaderArrow.classList.add("down");
                    }
                }

                for (let e of document.querySelectorAll('input[type="range"].slider-progress')) {
                    e.style.setProperty('--value', e.value);
                    e.style.setProperty('--min', e.min == '' ? '10' : e.min);
                    e.style.setProperty('--max', e.max == '' ? '90  ' : e.max);
                    e.addEventListener('input', () => e.style.setProperty('--value', e.value));
                }
            </script>
        </div>
    </body>
</html>