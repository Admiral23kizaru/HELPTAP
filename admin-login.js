// admin-login.js

document.addEventListener('keydown', function(e) {
    if (e.altKey && e.shiftKey && e.key.toLowerCase() === 'a') {
        showAdminLoginModal();
    }
});

function showAdminLoginModal() {
    // Prevent multiple modals
    if (document.getElementById('adminLoginModal')) return;

    const modal = document.createElement('div');
    modal.id = 'adminLoginModal';
    modal.innerHTML = `
        <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:1050;display:flex;align-items:center;justify-content:center;">
            <div style="background:#fff;padding:2rem 1.5rem;border-radius:16px;max-width:350px;width:100%;box-shadow:0 8px 32px rgba(31,38,135,0.13);position:relative;">
                <button id="closeAdminModal" style="position:absolute;top:10px;right:10px;border:none;background:transparent;font-size:1.5rem;cursor:pointer;">&times;</button>
                <h4 class="mb-3 text-center"><i class="fa-solid fa-user-shield text-primary"></i> Admin Login</h4>
                <form action="admin/login.php" method="POST">
                    <div class="mb-3">
                        <label for="adminEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="adminEmail" name="email" required value="admin@gmail.com">
                    </div>
                    <div class="mb-3">
                        <label for="adminPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="adminPassword" name="password" required value="admin123">
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Login</button>
                </form>
            </div>
        </div>
    `;
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    modal.style.zIndex = '1050';
    modal.style.display = 'block';
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    document.getElementById('closeAdminModal').onclick = function() {
        closeAdminLoginModal();
    };
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeAdminLoginModal();
    });
}

function closeAdminLoginModal() {
    let modal = document.getElementById('adminLoginModal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
} 