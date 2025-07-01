<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerMenu = document.getElementById('hamburgerMenu');
        const sidebar = document.querySelector('.sidebar, .dosen-sidebar, .mahasiswa-sidebar');
        const mainContent = document.querySelector('.main-content, .dosen-main-content, .mahasiswa-main-content');

        if (hamburgerMenu && sidebar) {
            hamburgerMenu.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // Optional: Close sidebar when clicking outside of it
        document.addEventListener('click', function(event) {
            if (sidebar && sidebar.classList.contains('show') && !sidebar.contains(event.target) && !hamburgerMenu.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
    });
</script>
</body>
</html>
