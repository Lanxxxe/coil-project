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
});
