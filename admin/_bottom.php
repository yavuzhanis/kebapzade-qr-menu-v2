</main></div>
<script>
document.querySelectorAll('[data-admin-theme-toggle]').forEach(function(btn) {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    var current = localStorage.getItem('kebapzade_admin_theme') || 'light';
    var next = current === 'dark' ? 'light' : 'dark';
    localStorage.setItem('kebapzade_admin_theme', next);
    document.documentElement.setAttribute('data-theme', next);
  });
});
</script>
</body></html>
