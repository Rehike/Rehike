if (!("setConfig" in window.yt))
{
    const ALERT_HTML =
`<div class="yt-alert yt-alert-default yt-alert-error">
    <div class="yt-alert-icon">
        <span class="icon master-sprite yt-sprite"></span>
    </div>
    <div class="yt-alert-content">
            <div class="yt-alert-message">
                Rehike has detected that you are probably using uBlock Origin. uBlock Origin's
                built-in filters prevent Rehike from working properly. You may either disable
                uBlock for YouTube (Rehike has a built-in ad blocker) or add the following to
                your uBlock filters:
                <br><br>
                <code>www.youtube.com#@#+js(set, ytcfg.data_.EXPERIMENT_FLAGS.web_streaming_watch, false)</code>
            </div>
    </div>
</div>`;
    document.getElementById("alerts").innerHTML += ALERT_HTML;
}