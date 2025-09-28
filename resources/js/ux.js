/**
 * UX helpers: scroll reveal animations, image loading fade-in,
 * and small microinteractions for pressable controls.
 */

function initUX() {
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
  function openModal() {
    modal.classList.remove('hidden');
    modal.setAttribute('data-open', 'true');
    modal.removeAttribute('data-closing');
    document.body.style.overflow = 'hidden';
  }
  function closeModal() {
    // play close animation then hide
    modal.removeAttribute('data-open');
    modal.setAttribute('data-closing', 'true');
    const surface = modal.querySelector('.modal-surface');
    const overlay = modal.querySelector('.modal-overlay');
    let finished = 0;
    const done = () => {
      finished += 1;
      if (finished >= 2) {
        modal.classList.add('hidden');
        modal.removeAttribute('data-closing');
        document.body.style.overflow = '';
      }
    };
    surface?.addEventListener('animationend', done, { once: true });
    overlay?.addEventListener('animationend', done, { once: true });
    // Fallback: ensure hide even if events don’t fire
    setTimeout(() => { if (!modal.classList.contains('hidden')) done(); if (!modal.classList.contains('hidden')) done(); }, 260);
  }
  closeBtn?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e) => {
    if (e.target === modal || e.target.closest('.modal-overlay')) closeModal();
  });
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

  // Place modal
  const placeModal = document.getElementById('place-modal');
  if (placeModal) {
    const pTitle = document.getElementById('place-modal-title');
    const pCountry = document.getElementById('place-modal-country');
    const pDesc = document.getElementById('place-modal-description');
    const pImg = document.getElementById('place-modal-image');
    const pClose = document.getElementById('place-modal-close');

    function openPlace() {
      placeModal.classList.remove('hidden');
      placeModal.setAttribute('data-open', 'true');
      placeModal.removeAttribute('data-closing');
      document.body.style.overflow = 'hidden';
    }
    function closePlace() {
      placeModal.removeAttribute('data-open');
      placeModal.setAttribute('data-closing', 'true');
      const surface = placeModal.querySelector('.modal-surface');
      const overlay = placeModal.querySelector('.modal-overlay');
      let finished = 0;
      const done = () => {
        finished += 1;
        if (finished >= 2) {
          placeModal.classList.add('hidden');
          placeModal.removeAttribute('data-closing');
          document.body.style.overflow = '';
        }
      };
      surface?.addEventListener('animationend', done, { once: true });
      overlay?.addEventListener('animationend', done, { once: true });
      setTimeout(() => { if (!placeModal.classList.contains('hidden')) done(); if (!placeModal.classList.contains('hidden')) done(); }, 260);
    }
    pClose?.addEventListener('click', closePlace);
    placeModal?.addEventListener('click', (e) => { if (e.target === placeModal || e.target.closest('.modal-overlay')) closePlace(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !placeModal.classList.contains('hidden')) closePlace(); });

    document.querySelectorAll('#cultural .experience-card').forEach(card => {
      function show(){
        pTitle.textContent = card.querySelector('h3')?.textContent?.trim() || '';
        // country badge text lives close by, but we can embed data attrs if needed; fallback to empty
        pCountry.textContent = card.querySelector('.badge')?.textContent?.trim() || '';
        // description paragraph in the card content block
        pDesc.textContent = card.querySelector('p')?.textContent?.trim() || '';
        const imgEl = card.querySelector('img');
        if (imgEl && imgEl.getAttribute('src')) { pImg.src = imgEl.getAttribute('src'); pImg.classList.remove('hidden'); }
        else { pImg.classList.add('hidden'); }
        openPlace();
      }
      card.addEventListener('click', show);
      card.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); show(); } });
    });
  }

  // Custom white-circle cursor (global)
  let cursor = document.getElementById('app-cursor');
  if (!cursor) {
    // Auto-inject if not present in the template
    cursor = document.createElement('div');
    cursor.id = 'app-cursor';
    cursor.setAttribute('aria-hidden', 'true');
    cursor.className = 'pointer-events-none fixed top-0 left-0 z-[200] hidden';
    document.body.appendChild(cursor);
  }
  if (cursor) {
    const enable = () => {
      document.body.classList.add('use-app-cursor');
      cursor.classList.remove('hidden');
    };
    const disable = () => {
      document.body.classList.remove('use-app-cursor');
      cursor.classList.add('hidden');
    };

    // Enable by default; honor users who prefer reduced motion (no trailing effects though)
    enable();

    // Follow pointer
    window.addEventListener('pointermove', (e) => {
      // Position the center of the circle at pointer
      const size = 18; // keep in sync with CSS
      cursor.style.transform = `translate(${e.clientX - size/2}px, ${e.clientY - size/2}px)`;
    });

    // Hide when leaving window
    window.addEventListener('pointerleave', () => disable());
    window.addEventListener('pointerenter', () => enable());
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initUX);
} else {
  // DOM already parsed; initialize immediately
  initUX();
}
