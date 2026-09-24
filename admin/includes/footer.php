<footer class="footer">

</footer>
</main> <!-- Cierra el content que abriremos en panel.php -->
</div> <!-- Cierra el main -->
</div> <!-- Cierra el app -->

<!-- Modales globales y Toasts se pueden incluir aquí en el futuro -->
<div class="toast-container position-fixed bottom-0 end-0 p-3"><div id="toast" class="toast border-0 shadow" role="alert"><div class="toast-body small"><i class="bi bi-check-circle-fill text-success me-2"></i><span id="toastText">Listo</span></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open')}
    function showToast(msg){document.getElementById('toastText').textContent=msg;new bootstrap.Toast(document.getElementById('toast'),{delay:2500}).show()}
</script>
</body>
</html>