<?php
/** TROCHA — Persistent Audio Player (iframe) */
header('Cache-Control: public, max-age=86400');
?><!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Player</title>
<style>body{margin:0;padding:0;background:transparent;overflow:hidden}</style>
</head>
<body>
<audio id="a" src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/mp3/caceria.mp3" preload="auto" loop></audio>
<script>
(function(){
    var a=document.getElementById('a'),playing=false,vol=0.6;a.volume=vol;
    function play(){a.play().then(function(){playing=true;notify()}).catch(function(){playing=false;notify()})}
    function pause(){a.pause();playing=false;notify()}
    function notify(){window.parent.postMessage({type:'trocha:audio',playing:playing,time:a.currentTime},'*')}
    window.addEventListener('message',function(e){
        if(!e.data||e.data.type!=='trocha:audio:cmd')return;
        switch(e.data.action){
            case 'play':if(!playing)play();break;
            case 'pause':if(playing)pause();break;
            case 'toggle':playing?pause():play();break;
            case 'status':notify();break;
            case 'volume':vol=e.data.value||0.6;a.volume=vol;break;
        }
    });
    document.addEventListener('visibilitychange',function(){if(!document.hidden&&playing&&a.paused)play()});
    setInterval(function(){if(playing)notify()},2000);
    window.parent.postMessage({type:'trocha:audio:ready'},'*');
})();
</script>
</body>
</html>
