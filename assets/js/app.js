document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('[data-nav-toggle]');
  const mobile = document.querySelector('[data-mobile-nav]');
  toggle?.addEventListener('click', () => {
    const open = mobile?.classList.toggle('is-open') ?? false;
    toggle.setAttribute('aria-expanded', String(open));
  });

  const rail = document.querySelector('.rail-inner');
  rail?.addEventListener('wheel', (event) => {
    if (Math.abs(event.deltaY) > Math.abs(event.deltaX)) {
      rail.scrollLeft += event.deltaY;
      event.preventDefault();
    }
  }, { passive: false });

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const reveal = [...document.querySelectorAll('[data-reveal]')];
  if (reduced || !('IntersectionObserver' in window)) {
    reveal.forEach((node) => node.classList.add('is-visible'));
  } else {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    reveal.forEach((node) => observer.observe(node));
  }

  if (!reduced && window.matchMedia('(pointer:fine)').matches) {
    document.querySelectorAll('[data-tilt]').forEach((card) => {
      card.addEventListener('pointermove', (event) => {
        const rect = card.getBoundingClientRect();
        const x = ((event.clientX - rect.left) / rect.width - .5) * 8;
        const y = ((event.clientY - rect.top) / rect.height - .5) * -8;
        card.style.setProperty('--rx', y + 'deg');
        card.style.setProperty('--ry', x + 'deg');
      });
      card.addEventListener('pointerleave', () => {
        card.style.setProperty('--rx', '0deg');
        card.style.setProperty('--ry', '0deg');
      });
    });
  }

  const header = document.querySelector('[data-header]');
  const syncHeader = () => header?.classList.toggle('is-scrolled', window.scrollY > 18);
  syncHeader();
  window.addEventListener('scroll', syncHeader, { passive: true });

  document.querySelectorAll('[data-remote-image]').forEach((image) => {
    image.addEventListener('error', () => image.classList.add('is-broken'));
  });

  const galleryMain = document.querySelector('[data-gallery-main]');
  const galleryImage = document.querySelector('[data-gallery-image]');
  const galleryCount = document.querySelector('[data-gallery-count]');
  document.querySelectorAll('[data-gallery-thumb]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!galleryMain) return;
      const url = button.dataset.imageUrl || '';
      if (galleryImage && url) {
        galleryImage.classList.remove('is-broken');
        galleryImage.src = url;
      }
      if (galleryCount) galleryCount.textContent = button.dataset.index || '1';
      document.querySelectorAll('[data-gallery-thumb]').forEach((item) => item.classList.remove('active'));
      button.classList.add('active');
    });
  });

  const modal = document.querySelector('[data-lead-modal]');
  const openModal = (button) => {
    if (!modal) return;
    const cityField = modal.querySelector('[data-lead-city]');
    if (cityField) cityField.value = button.dataset.city || cityField.value || '';
    modal.querySelector('[data-lead-vertical]').value = button.dataset.vertical || '';
    modal.querySelector('[data-lead-entity]').value = button.dataset.entity || '';
    modal.querySelector('[data-lead-type]').value = button.dataset.type || 'enquiry';
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('input[name="name"]')?.focus();
  };
  document.querySelectorAll('[data-open-lead]').forEach((button) => button.addEventListener('click', () => openModal(button)));
  document.querySelector('[data-close-modal]')?.addEventListener('click', () => {
    modal?.classList.remove('is-open');
    modal?.setAttribute('aria-hidden', 'true');
  });
  modal?.addEventListener('click', (event) => {
    if (event.target === modal) {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
    }
  });

  const dock = document.querySelector('[data-compare-dock]');
  const compareBoxes = [...document.querySelectorAll('[data-compare]')];
  const syncCompare = () => {
    const checked = compareBoxes.filter((box) => box.checked);
    if (checked.length > 3) {
      checked[checked.length - 1].checked = false;
      return syncCompare();
    }
    if (!dock) return;
    dock.querySelector('[data-compare-count]').textContent = String(checked.length);
    dock.querySelector('[data-compare-names]').textContent = checked.length
      ? checked.map((box) => box.dataset.name).join(' · ')
      : 'Choose up to three profiles';
    dock.classList.toggle('is-open', checked.length > 0);
    dock.setAttribute('aria-hidden', checked.length > 0 ? 'false' : 'true');
  };
  compareBoxes.forEach((box) => box.addEventListener('change', syncCompare));
  dock?.querySelector('[data-clear-compare]')?.addEventListener('click', () => {
    compareBoxes.forEach((box) => { box.checked = false; });
    syncCompare();
  });
  dock?.querySelector('[data-go-compare]')?.addEventListener('click', () => {
    const selected = compareBoxes.filter((box) => box.checked).map((box) => box.dataset.slug);
    if (selected.length < 2) return;
    const vertical = dock.dataset.vertical || '';
    const city = dock.dataset.city || '';
    const area = dock.dataset.area || '';
    const base = window.location.pathname.includes('/excompass/') ? '/excompass/' : '/';
    const params = new URLSearchParams({ vertical, items: selected.join(',') });
    if (city) params.set('city', city);
    if (area) params.set('area', area);
    window.location.href = base + 'compare.php?' + params.toString();
  });
});
