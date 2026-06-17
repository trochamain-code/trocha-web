<?php
defined('ABSPATH') || exit;

function trocha_enqueue() {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    // Google Fonts — Special Elite + Bebas Neue
    wp_enqueue_style('trocha-fonts', 'https://fonts.googleapis.com/css2?family=Special+Elite&family=Bebas+Neue&display=swap', [], null);

    // Use file modification time so CSS/JS edits always bust the cache.
    $cani_ver = file_exists($dir . '/assets/css/cani.css') ? filemtime($dir . '/assets/css/cani.css') : '1.0.0';
    wp_enqueue_style('trocha-cani', $uri . '/assets/css/cani.css', [], $cani_ver);

    if (class_exists('WooCommerce')) {
        $wc_ver = file_exists($dir . '/assets/css/woocommerce.css') ? filemtime($dir . '/assets/css/woocommerce.css') : '1.0.0';
        wp_enqueue_style('trocha-woocommerce', $uri . '/assets/css/woocommerce.css', ['trocha-cani'], $wc_ver);
    }

    $js_ver = file_exists($dir . '/assets/js/main.js') ? filemtime($dir . '/assets/js/main.js') : '1.0.0';
    wp_enqueue_script('trocha-main', $uri . '/assets/js/main.js', [], $js_ver, true);

    // Slider de últimas prendas — inline, sin dependencias
    if (is_front_page() || is_home()) {
        wp_register_script('trocha-slider', false, [], false, true);
        wp_enqueue_script('trocha-slider');
        wp_add_inline_script('trocha-slider', '
window.addEventListener("load",function(){
  var wrap=document.getElementById("trochaSlider");
  var track=document.getElementById("trochaTrack");
  if(!wrap||!track)return;
  var allItems=track.querySelectorAll(".trs__item");
  var realItems=track.querySelectorAll(".trs__item:not(.trs__item--clone)");
  var total=realItems.length;
  if(!total)return;
  function iw(){return allItems[0]?allItems[0].getBoundingClientRect().width:0;}
  var offset=0;
  function init(){
    var w=iw();
    if(w===0){setTimeout(init,100);return;}
    offset=w*total;
    track.style.transition="none";
    track.style.transform="translateX(-"+offset+"px)";
  }
  init();
  window.addEventListener("resize",function(){setTimeout(init,50);});
  var sx=0,so=0,drag=false,moved=false;
  function pStart(x){drag=true;moved=false;sx=x;so=offset;track.style.transition="none";}
  function pMove(x){if(!drag)return;var d=x-sx;if(Math.abs(d)>3)moved=true;offset=so-d;track.style.transform="translateX(-"+offset+"px)";}
  function pEnd(){if(!drag)return;drag=false;snap();}
  wrap.addEventListener("mousedown",function(e){pStart(e.clientX);});
  window.addEventListener("mousemove",function(e){pMove(e.clientX);});
  window.addEventListener("mouseup",function(){pEnd();});
  wrap.addEventListener("touchstart",function(e){pStart(e.touches[0].clientX);},{passive:true});
  wrap.addEventListener("touchmove",function(e){pMove(e.touches[0].clientX);},{passive:true});
  wrap.addEventListener("touchend",function(){pEnd();});
  wrap.addEventListener("click",function(e){if(moved){e.preventDefault();e.stopPropagation();}},true);
  function snap(){
    var w=iw(),bw=w*total;
    if(w===0)return;
    var idx=Math.round(offset/w);
    offset=idx*w;
    track.style.transition="transform 0.28s cubic-bezier(.25,.46,.45,.94)";
    track.style.transform="translateX(-"+offset+"px)";
    setTimeout(function(){
      if(offset<w){offset+=bw;track.style.transition="none";track.style.transform="translateX(-"+offset+"px)";}
      else if(offset>bw*2-w){offset-=bw;track.style.transition="none";track.style.transform="translateX(-"+offset+"px)";}
    },300);
  }
  // Flechas desktop
  var btnPrev=document.getElementById("trsArrowPrev");
  var btnNext=document.getElementById("trsArrowNext");
  function slideBy(dir){
    var w=iw();
    if(w===0)return;
    offset+=dir*w;
    track.style.transition="transform 0.28s cubic-bezier(.25,.46,.45,.94)";
    track.style.transform="translateX(-"+offset+"px)";
    setTimeout(function(){
      if(offset<w){offset+=bw;track.style.transition="none";track.style.transform="translateX(-"+offset+"px)";}
      else if(offset>bw*2-w){offset-=bw;track.style.transition="none";track.style.transform="translateX(-"+offset+"px)";}
    },300);
  }
  if(btnPrev)btnPrev.addEventListener("click",function(){slideBy(-1);});
  if(btnNext)btnNext.addEventListener("click",function(){slideBy(1);});
});
        ');
    }

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'trocha_enqueue');

