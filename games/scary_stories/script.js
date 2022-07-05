{
    let plot = "";
    let plot_request = new XMLHttpRequest();

    plot_request.onreadystatechange = function() {
        if (this.readyState == 4){
            if (this.status == 200) {
                plot = plot_request.responseText;
            }
        }
    };

    plot_request.open("GET", "games/scary_stories/plot.xml", false);

    plot_request.send();

    let xmlDoc = $.parseXML(plot);
    let $xml = $( xmlDoc );
    let $scenes = $xml.find( "scenes" );

    let game_scene = null;
    let scene_container = null;
    let image_container = null;
    let story_container = null;

    let images = [];
    let stories = [];

    pageAction = function(gameContext) {
        game_scene = gameContext;

        create_scene_container();

        document.addEventListener("keyup", keyUpHandler, false);

        set_game_drop(function(){
            document.removeEventListener("keyup", keyUpHandler);
        });
    };

    function keyUpHandler(e) {
        if(e.code == "Space") {
            next_scene();
        }
    }

    function create_scene_container() {
        game_scene.classList.add("game_body");
        game_scene.onclick = next_scene;

        scene_container = document.createElement('div');
        scene_container.classList.add("scene_container");

        image_container = document.createElement('div');
        image_container.classList.add("image_container");

        scene_container.appendChild(image_container);

        story_container = document.createElement('div');
        story_container.classList.add("story_container");

        let image_overlay = document.createElement('div');
        image_overlay.classList.add("image_overlay");

        story = document.createElement('div');
        story.classList.add("story");
        image_overlay.appendChild(story);

        story_container.appendChild(image_overlay);

        scene_container.appendChild(story_container);

        game_scene.appendChild(scene_container);

        get_scene(0);
        get_scene(1);

        story.innerHTML = stories[0];
    }

    let pages_count = $scenes.find("scene").size();
    let current_scene = 0;

    let step = 0;
    let speed = 5;
    let current_position = 0;

    let changing = false;
    let id = 0;
    function next_scene(){
        if (images.length > 1){
            if (!changing){
                changing = true;

                if (current_scene < pages_count-2){
                    get_scene(current_scene+2);
                }

                story.innerHTML = "";
                id = setInterval(function(){
                    if (step < 20){
                        current_position += speed;
                        for (var i = 0; i < 2; i++){
                            images[i].style.left = "-" + current_position + "%";
                        }
                        step++;
                    }
                    else{
                        clearInterval(id);

                        current_scene+=1;

                        changing = false;
                        step = 0;
                        current_position = 0;

                        image_container.removeChild(images[0]);
                        images.splice(0,1);
                        images[0].style.left = "0%";

                        stories.splice(0,1);
                        story.innerHTML = stories[0];
                    }
                },20);
            }
        }
    }

    function get_scene(scene_id){
        let $scene = $scenes.find("scene").get(scene_id);
        let $src = $scene.getAttribute("src").toString();
        let $text = $scene.innerHTML.toString();

        let img = document.createElement('img');
        img.setAttribute("src",$src);
        img.setAttribute("loading","lazy");
        images.push(img);
        image_container.appendChild(img);

        stories.push($text);
    }
}