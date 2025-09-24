/**
 * Automatic contrast detection for cards with class .experience-card
 * Adds .on-light-bg when average luminance of background image > threshold.
 * Priority of image source:
 *  1. data-bg-image attribute (string URL)
 *  2. <img data-card-bg>
 *  3. first <img> descendant
 */
(function(){
  const cards = document.querySelectorAll('.experience-card');
  if(!cards.length) return;

  const THRESHOLD = 0.60; // tweak to taste (0..1)

  function analyze(img, card){
    if(!img.complete || !img.naturalWidth){
      img.addEventListener('load', () => analyze(img, card), { once:true });
      return;
    }
    try {
      const w = 48;
      const h = Math.max(8, Math.round((img.naturalHeight / img.naturalWidth) * w));
      const canvas = document.createElement('canvas');
      canvas.width = w; canvas.height = h;
      const ctx = canvas.getContext('2d', { willReadFrequently: true });
      ctx.drawImage(img, 0, 0, w, h);
      const data = ctx.getImageData(0,0,w,h).data;
      let sum = 0; const pixels = data.length / 4;
      for(let i=0;i<data.length;i+=4){
        const r = data[i] / 255, g = data[i+1] / 255, b = data[i+2] / 255;
        sum += 0.2126*r + 0.7152*g + 0.0722*b; // luminance
      }
      const avg = sum / pixels;
      if(avg > THRESHOLD) card.classList.add('on-light-bg'); else card.classList.remove('on-light-bg');
    } catch(e){ /* silent */ }
  }

  function sourceFor(card){
    const attr = card.getAttribute('data-bg-image');
    if(attr){
      const img = new Image(); img.src = attr; analyze(img, card); return;
    }
    const tagged = card.querySelector('img[data-card-bg]');
    if(tagged){ analyze(tagged, card); return; }
    const first = card.querySelector('img');
    if(first) analyze(first, card);
  }

  function process(){ cards.forEach(sourceFor); }
  if('requestIdleCallback' in window){ requestIdleCallback(process, { timeout:1500 }); } else { setTimeout(process, 0); }

  // Observe dynamically added cards
  const mo = new MutationObserver(muts => {
    muts.forEach(m => m.addedNodes.forEach(node => {
      if(!(node instanceof HTMLElement)) return;
      if(node.matches('.experience-card')) sourceFor(node);
      node.querySelectorAll?.('.experience-card').forEach(sourceFor);
    }));
  });
  mo.observe(document.body, { childList:true, subtree:true });
})();
