<?php
/**
 * wow.js – Custom IntersectionObserver based animation engine,
 * modal & drawer controllers, live table search, and form submission animation.
 */

declare(strict_types=1);

// Ensure script runs after DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // ---------- WOW (scroll) animations ----------
    const wowElements = document.querySelectorAll('.wow');
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                obs.unobserve(entry.target);
            }
        });
    }, {threshold: 0.2});
    wowElements.forEach(el => observer.observe(el));

    // ---------- Modal handling ----------
    const modal = document.getElementById('admin-modal');
    const openModalBtn = document.getElementById('open-admin-modal');
    const closeModalBtn = modal?.querySelector('.close');
    if (modal && openModalBtn) {
        openModalBtn.addEventListener('click', () => modal.classList.add('active'));
        closeModalBtn?.addEventListener('click', () => modal.classList.remove('active'));
        // Close when clicking outside content
        modal.addEventListener('click', e => {
            if (e.target === modal) modal.classList.remove('active');
        });
    }

    // ---------- Drawer handling (side navigation) ----------
    const drawer = document.getElementById('admin-drawer');
    const drawerToggle = document.getElementById('drawer-toggle');
    const drawerClose = drawer?.querySelector('.close');
    if (drawer && drawerToggle) {
        drawerToggle.addEventListener('click', () => drawer.classList.add('active'));
        drawerClose?.addEventListener('click', () => drawer.classList.remove('active'));
        // Close on overlay click
        drawer.addEventListener('click', e => {
            if (e.target === drawer) drawer.classList.remove('active');
        });
    }

    // ---------- Live table search (admin dashboard) ----------
    const searchInput = document.getElementById('beneficiary-search');
    const tableRows = document.querySelectorAll('#beneficiaries-table tbody tr');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // ---------- Form submission animation (beneficiary registration) ----------
    const regForm = document.getElementById('beneficiary-form');
    if (regForm) {
        regForm.addEventListener('submit', function (e) {
            // Let server handle validation; just add a temporary animation class
            const btn = regForm.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            regForm.classList.add('submitting');
        });
    }
});
?>