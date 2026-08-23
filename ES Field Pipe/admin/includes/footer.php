    </main>

    <footer class="bg-white border-top py-3 px-4 text-center text-muted small mt-auto">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <div>
                &copy; <?= date('Y') ?> <strong><?= htmlspecialchars(get_setting('site_name', 'Esfield Pipe Pvt. Ltd.')) ?></strong> — Enterprise DWC HDPE Management Platform.
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-secondary border">PHP <?= phpversion() ?></span>
                <span class="badge bg-light text-success border"><i class="fa-solid fa-circle text-success me-1" style="font-size: 0.5rem;"></i> System Operational</span>
            </div>
        </div>
    </footer>
</div>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar && backdrop) {
        sidebar.classList.toggle('show');
        backdrop.classList.toggle('show');
    }
}
</script>

</body>
</html>
