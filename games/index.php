<!doctype html>
<html>
    <head>
        <meta charset = "utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style><?php include_once("main_styles.css");?></style>

        <script>
            function onNavigationMenuClick(){
                let navigationBar = document.getElementById("NavigationBar");

                if (navigationBar.classList.contains("expanded")){
                    navigationBar.classList.remove("expanded");
                }
                else{
                    navigationBar.classList.add("expanded");
                }
            }
        </script>
    </head>

    <body>
        <?php include("navigation_bar.html"); ?>

        <div class="Page">
            <style>
                .game_list_table {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                }

                .game_list_table .cell {
                    display: block;
                    width: 33%;
                    max-width: 300px;
                    min-width: 150px;
                }

                @media (max-width: 600px) {
                    .game_list_table .cell {
                        width: 50%;
                    }
                }

                @media (max-width: 380px) {
                    .game_list_table .cell {
                        width: 100%;
                    }
                }

                .game_list_table .cell .frame {
                    padding: 8px;
                    box-sizing: border-box;
                }

                .game_list_table .cell .item {
                    border-radius: 4px;
                    color: rgb(90, 90, 90);
                    background-color:rgb(236, 236, 236);
                    width: 100%;
                    height: 0;
                    padding-top: 100%;
                    position: relative;
                }

                .game_list_table .cell .item:hover {
                    background-color: rgb(199, 199, 199);
                    color: rgb(0, 0, 0);
                    cursor: pointer;
                }

                .game_list_table .cell .item .content {
                    position: absolute;
                    top: 0;
                    left: 0;
                    padding: 10px;
                }

                .header ul {
                    margin: 0px;
                    padding: 0px;
                    width: 100%;
                    height: 50px;
                    display: table;
                    table-layout: auto;
                }
                .header li {
                    display: table-cell;
                    text-align: center;
                    vertical-align: middle;
                }

                .header .text{
                    width: 1%;
                    white-space: nowrap;
                    padding-right: 18px;
                    padding-left: 18px;
                    font-size: 22px;
                }
            </style>
            <div id="GameList">
                <div class="header">
                    <ul>
                        <li><hr size='2' color='black'/></li>
                        <li class="text">Игры на JS</li>
                        <li><hr size='2' color='black'/></li>
                    </ul>
                </div>

                <?php
                    $xmldoc = new DOMDocument();
                    $xmldoc->load("./games/games.xml");

                    $games = $xmldoc->getElementsByTagName("games")->item(0);

                    if ($games == null){
                        echo "Игр пока нет";
                    }
                    else{
                        echo "<div class='game_list_table'>";

                        foreach($games->getElementsByTagName("game") as $game){
                            $name = $game->attributes[0]->nodeValue;
                            $path = $game->attributes[1]->nodeValue;

                            create_game_item($name, $game->nodeValue, $path);
                        }
                        echo "<div class='cell'></div>";
                        echo "<div class='cell'></div>";

                        echo "</div>";
                    }

                    function create_game_item($name, $description, $path){
                        echo "<div class='cell'>";
                            echo "<div class='frame'>";
                                echo "<a href='games/$path'>";
                                    echo "<div class='item'>";
                                        echo "<div class='content'>";
                                            echo "<div style='font-size:26px'>" .$name. "</div>";
                                            echo "<div style='font-size:20px'>" .$description. "</div>";
                                        echo "</div>";
                                    echo "</div>";
                                echo "</a>";
                            echo "</div>";
                        echo "</div>";
                    }
                ?>

                <div class="header">
                    <ul>
                        <li><hr size='2' color='black'/></li>
                        <li class="text">Игры на JS</li>
                        <li><hr size='2' color='black'/></li>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>