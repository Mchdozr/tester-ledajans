/* LEDAJANS R2: lazy video yedek hidrasyonu (sayfadaki mevcut IO script'i önce çalışır) */
(function(){var vids=[].slice.call(document.querySelectorAll('video[data-ldj-lazy]'));if(!vids.length)return;
var rm=window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches;
function hydrate(v){if(v.dataset.hydrated==='1')return;var n=0,s=v.getAttribute('data-ldj-src');if(s&&!v.getAttribute('src')){v.setAttribute('src',s);n++}
[].forEach.call(v.querySelectorAll('source'),function(x){var u=x.getAttribute('data-src')||x.getAttribute('data-ldj-src');if(u&&!x.getAttribute('src')){x.setAttribute('src',u);n++}});
v.dataset.hydrated='1';if(n)v.load();}
if(rm)vids.forEach(function(v){v.removeAttribute('autoplay')});
if(!('IntersectionObserver' in window)){vids.forEach(hydrate);return;}
var io=new IntersectionObserver(function(es){es.forEach(function(e){if(!e.isIntersecting)return;var v=e.target;setTimeout(function(){hydrate(v);if(v.autoplay&&!rm&&v.paused){var p=v.play();p&&p.catch&&p.catch(function(){})}},60)})},{rootMargin:'300px 0px'});
vids.forEach(function(v){io.observe(v)});})();

/* LEDAJANS R3: masaüstü sticky header yedeği (tema JS'i çalışmazsa aynı sınıfı uygular; yükseklik rezerve) */
(function(){var mq=window.matchMedia&&matchMedia('(min-width:1025px)');if(!mq)return;var h=document.documentElement,w,menu,hh=0,tick=false,wasFixed=false;
function setup(){menu=document.querySelector('.gv-sticky-menu');if(!menu)return false;w=menu.parentElement;if(!w||!w.classList.contains('gv-sticky-wrapper'))return false;return true}
function measure(){if(!w)return;var fixed=w.classList.contains('is-fixed');if(!fixed){hh=menu.offsetHeight;if(hh)w.style.setProperty('--ldj-hdr-h',hh+'px')}}
function upd(){tick=false;if(!mq.matches||!w)return;var top=w.getBoundingClientRect().top+window.pageYOffset,y=window.pageYOffset,fix=y>top;
if(fix&&!w.classList.contains('is-fixed')){if(!hh)measure();w.classList.add('is-fixed');w.style.height=hh+'px'}
else if(!fix&&w.classList.contains('is-fixed')){w.classList.remove('is-fixed');w.style.height='auto'}
var nowFixed=w.classList.contains('is-fixed');if(nowFixed&&!wasFixed&&y>hh*2)h.classList.add('ldj-hdr-anim');if(!nowFixed)h.classList.remove('ldj-hdr-anim');wasFixed=nowFixed}
function onS(){if(!tick){tick=true;requestAnimationFrame(upd)}}
function init(){if(!setup())return;measure();upd();window.addEventListener('scroll',onS,{passive:true});window.addEventListener('resize',function(){if(!w.classList.contains('is-fixed'))measure();onS()},{passive:true})}
if(document.readyState==='complete')setTimeout(init,0);else window.addEventListener('load',function(){setTimeout(init,0)});})();
