/**
 * Southeast Asia Map interactions
 * - Hover shows tooltip
 * - Click opens modal with famous place (text only)
 */

document.addEventListener('DOMContentLoaded', () => {
  const svg = document.getElementById('sea-map');
  if (!svg) return;

  const tooltip = document.getElementById('map-tooltip');
  const modal = document.getElementById('map-modal');
  const modalCountry = document.getElementById('map-modal-country');
  const modalTitle = document.getElementById('map-modal-title');
  const modalDesc = document.getElementById('map-modal-description');

  const regions = svg.querySelectorAll('.map-region');
  let hoverTimer = null;

  // Helper: position tooltip
  function positionTooltip(evt) {
    if (!tooltip) return;
    const padding = 12;
    const rect = svg.getBoundingClientRect();
    // mouse position relative to svg
    const x = (evt.clientX - rect.left) + 14;
    const y = (evt.clientY - rect.top) + 14;
    tooltip.style.left = `${Math.min(rect.width - 200, Math.max(padding, x))}px`;
    tooltip.style.top = `${Math.min(rect.height - 40, Math.max(padding, y))}px`;
  }

  // Helper: open modal
  function openModal({ country, title, description }) {
    if (!modal) return;
    modalCountry.textContent = country || '';
    modalTitle.textContent = title || '';
    modalDesc.textContent = description || '';
    modal.classList.remove('hidden');
    // trap focus to first close button
    const firstBtn = modal.querySelector('[data-close]');
    if (firstBtn) firstBtn.focus({ preventScroll: true });
    document.body.style.overflow = 'hidden';
  }

  // Helper: close modal
  function closeModal() {
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  }

  // Wire close actions
  if (modal) {
    modal.addEventListener('click', (e) => {
      const target = e.target;
      if (!(target instanceof Element)) return;
      if (target.dataset.close === 'backdrop' || target.dataset.close === 'button') {
        closeModal();
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
        closeModal();
      }
    });
  }

  // Region interactions
  regions.forEach((el) => {
    // ARIA
    el.setAttribute('role', 'button');
    el.setAttribute('aria-label', `${el.dataset.country} — ${el.dataset.region}`);

    el.addEventListener('mouseenter', (evt) => {
      if (!tooltip) return;
      tooltip.textContent = `${el.dataset.region}, ${el.dataset.country}`;
      tooltip.classList.remove('hidden');
      positionTooltip(evt);
      el.classList.add('is-hovered');

      // Open modal shortly after hover
      clearTimeout(hoverTimer);
      hoverTimer = setTimeout(() => {
        openModal({
          country: el.dataset.country,
          title: el.dataset.title,
          description: el.dataset.description,
        });
      }, 180);
    });

    el.addEventListener('mousemove', (evt) => {
      positionTooltip(evt);
    });

    el.addEventListener('mouseleave', () => {
      if (!tooltip) return;
      tooltip.classList.add('hidden');
      el.classList.remove('is-hovered');
      clearTimeout(hoverTimer);
    });

    el.addEventListener('click', () => {
      openModal({
        country: el.dataset.country,
        title: el.dataset.title,
        description: el.dataset.description,
      });
    });

    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openModal({
          country: el.dataset.country,
          title: el.dataset.title,
          description: el.dataset.description,
        });
      }
    });
  });
});
