<?php ?>
<script>

window.fatalDisableRehikeOnce = function() {
    var currentUrl = window.location.href;

    var urlParser = new URL(currentUrl);
    urlParser.searchParams.set("enable_polymer", "1");
    
    window.location.href = urlParser.toString();
};

window.fatalDisableRehike = function() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/rehike/update_config");
    xhr.onload = function() {
        if (200 == xhr.status)
            window.location.reload();
    };
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify({
        "hidden.disableRehike": true
    }));
};

</script>