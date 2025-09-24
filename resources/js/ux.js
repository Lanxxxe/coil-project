/**
 * UX helpers: scroll reveal animations, image loading fade-in,
 * and small microinteractions for pressable controls.
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1) Scroll reveal using IntersectionObserver
  const revealEls = Array.from(document.querySelectorAll('.reveal-on-scroll'));
  if (revealEls.length) {
    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          obs.unobserve(entry.target);
        }
      });
    }, { root: null, threshold: 0.15 });

    revealEls.forEach(el => io.observe(el));
  }

  // 2) Image loading fade-in
  document.querySelectorAll('img').forEach(img => {
    // Set loading attribute if missing (except for explicitly "eager")
    if (!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
    img.setAttribute('data-loading', '');
    if (img.complete) {
      img.removeAttribute('data-loading');
    } else {
      img.addEventListener('load', () => img.removeAttribute('data-loading'), { once: true });
      img.addEventListener('error', () => img.removeAttribute('data-loading'), { once: true });
    }
  });

  // 3) Press feedback for buttons/links
  const pressables = document.querySelectorAll('.pressable');
  pressables.forEach(el => {
    const add = () => el.classList.add('is-pressed');
    const remove = () => el.classList.remove('is-pressed');
    el.addEventListener('pointerdown', add);
    el.addEventListener('pointerup', remove);
    el.addEventListener('pointerleave', remove);
    el.addEventListener('blur', remove);
  });

  // Food modal
  const modal = document.getElementById('food-modal');
  const title = document.getElementById('food-modal-title');
  const country = document.getElementById('food-modal-country');
  const desc = document.getElementById('food-modal-description');
  const img = document.getElementById('food-modal-image');
  const closeBtn = document.getElementById('food-modal-close');
  function openModal() { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
  function closeModal() { modal.classList.add('hidden'); document.body.style.overflow = ''; }
  closeBtn?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e) => { if (e.target === modal || e.target.closest('.absolute.inset-0.bg-black/60')) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });
  document.querySelectorAll('#culinary .experience-card').forEach(card => {
    function show(){
      title.textContent = card.getAttribute('data-food-title') || '';
      country.textContent = card.getAttribute('data-food-country') || '';
      desc.textContent = card.getAttribute('data-food-description') || '';
      const src = card.getAttribute('data-food-image');
      if (src) { img.src = src; img.classList.remove('hidden'); } else { img.classList.add('hidden'); }
      openModal();
    }
    card.addEventListener('click', show);
    card.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); show(); } });
  });
});
