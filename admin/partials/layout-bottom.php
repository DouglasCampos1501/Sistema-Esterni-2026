    </main>
</div>
<script src="/assets/js/admin-i18n.js"></script>
<script>
(function () {
    var sidebar = document.getElementById('admin-sidebar');
    var toggle = document.getElementById('sidebar-toggle');
    var collapsed = localStorage.getItem('admin_sidebar_collapsed') === '1';
    sidebar.classList.toggle('collapsed', collapsed);
    document.documentElement.classList.remove('sidebar-collapsed-init');

    toggle.addEventListener('click', function () {
        collapsed = !collapsed;
        sidebar.classList.toggle('collapsed', collapsed);
        localStorage.setItem('admin_sidebar_collapsed', collapsed ? '1' : '0');
    });
})();
</script>
</body>
</html>
