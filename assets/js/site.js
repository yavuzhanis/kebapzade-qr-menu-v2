document.addEventListener('DOMContentLoaded', () => {
  const search = document.querySelector('[data-search]');
  const chips = [...document.querySelectorAll('[data-filter]')];
  const categories = [...document.querySelectorAll('[data-category]')];

  function normalize(s){ return (s || '').toLocaleLowerCase('tr-TR'); }

  function apply(){
    const term = normalize(search?.value || '');
    const active = document.querySelector('[data-filter].active')?.dataset.filter || 'all';

    categories.forEach(cat => {
      const catMatch = active === 'all' || cat.dataset.category === active;
      let visibleCount = 0;
      cat.querySelectorAll('[data-item]').forEach(item => {
        const textMatch = normalize(item.dataset.search).includes(term);
        const visible = catMatch && textMatch;
        item.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
      });
      cat.style.display = (catMatch && visibleCount > 0) ? '' : 'none';
    });
  }

  search?.addEventListener('input', apply);
  chips.forEach(chip => chip.addEventListener('click', () => {
    chips.forEach(x => x.classList.remove('active'));
    chip.classList.add('active');
    apply();
    if (window.innerWidth > 700 && chip.dataset.filter !== 'all') {
      document.getElementById('cat-' + chip.dataset.filter)?.scrollIntoView({behavior:'smooth', block:'start'});
    }
  }));
});
