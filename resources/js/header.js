// Toggle solid header background after scrolling past the hero
(function(){
  const header = document.getElementById('site-header');
  if(!header) return;
  const hero = document.getElementById('hero-carousel');
  const thresholdPx = 48;

  function setSolid(on){
    header.classList.toggle('header--solid', !!on);
  }

  if(hero && 'IntersectionObserver' in window){
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => setSolid(!e.isIntersecting));
    }, { rootMargin: `-${thresholdPx}px 0px 0px 0px`, threshold: 0 });
    obs.observe(hero);
  } else {
    // Fallback: use scroll position
    const onScroll = () => setSolid(window.scrollY > thresholdPx);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive:true });
  }
})();
