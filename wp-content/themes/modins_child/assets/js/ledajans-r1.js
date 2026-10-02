/* LEDAJANS R1: aktif menü + scroll-reveal (vanilla, ~1KB) */
(function(){var d=document,h=d.documentElement;
var norm=function(u){try{var x=new URL(u,location.href);return x.origin+x.pathname.replace(/\/+$/,'/')}catch(e){return ''}},here=norm(location.href);
d.querySelectorAll('.gva-main-menu a[href]').forEach(function(a){var href=a.getAttribute('href');if(!href||href==='#'||a.closest('.pll-parent-menu-item,.lang-item'))return;if(norm(a.href)!==here)return;var li=a.closest('li');li&&li.classList.add('ldj-active');var top=a.closest('.gva-main-menu>li');top&&top.classList.add('ldj-active')});
if(!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches)return;
var els=[].slice.call(d.querySelectorAll('#wp-main-content .elementor-top-section, #wp-main-content .e-con.e-parent')).filter(function(e){return !e.closest('header')&&e.offsetHeight>0&&e.getBoundingClientRect().top>innerHeight*0.9});
if(!els.length)return;h.classList.add('ldj-rv-on');
var io=new IntersectionObserver(function(en){en.forEach(function(x){if(!x.isIntersecting)return;var t=x.target;t.classList.add('ldj-in');io.unobserve(t);setTimeout(function(){t.classList.remove('ldj-rv','ldj-in')},900)})},{rootMargin:'0px 0px -8% 0px',threshold:0});
els.forEach(function(e){e.classList.add('ldj-rv');io.observe(e)});
})();
