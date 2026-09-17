window.init = async function() {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const backBtn = document.getElementById('btn-back-invoices');
    const container = document.getElementById('invoices-container');
    const bannerTitle = document.getElementById('invoices-total-banner');
    const filterPills = document.querySelectorAll('.filter-pill');

    if (backBtn) {
        backBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.navigateTo('dashboard/home');
        });
    }

    let allInvoices = [];

    async function loadInvoices() {
        try {
            const response = await fetch(`${window.API_BASE}/api/mobile/invoices?user_id=${user.id}`, {
                method: 'GET',
                credentials: 'include'
            });
            const data = await response.json();

            if (data.status === 1 && data.invoices) {
                allInvoices = data.invoices;

                let dueTotal = 0;
                allInvoices.forEach(inv => {
                    if (parseInt(inv.payment_status, 10) === 1) {
                        dueTotal += parseFloat(inv.total_amount || 0);
                    }
                });

                if (bannerTitle) {
                    bannerTitle.textContent = dueTotal > 0 ? `$${dueTotal.toLocaleString('en-US', {minimumFractionDigits: 2})} Outstanding` : 'All Accounts Settled';
                }

                renderInvoices('all');
            }
        } catch (err) {
            console.error('Error fetching invoices:', err);
            container.innerHTML = '<div class="empty-state"><div class="empty-icon">⚠️</div><p>Failed to load invoices.</p></div>';
        }
    }

    function renderInvoices(filter) {
        if (!container) return;

        let filtered = allInvoices;
        if (filter === 'due') {
            filtered = allInvoices.filter(i => parseInt(i.payment_status, 10) === 1);
        } else if (filter === 'paid') {
            filtered = allInvoices.filter(i => parseInt(i.payment_status, 10) === 2);
        }

        if (filtered.length === 0) {
            container.innerHTML = '<div class="empty-state"><div class="empty-icon">📄</div><p>No invoices matching this filter.</p></div>';
            return;
        }

        container.innerHTML = filtered.map(inv => {
            const isPaid = parseInt(inv.payment_status, 10) === 2;
            const badgeClass = isPaid ? 'badge-green' : 'badge-red';
            const badgeLabel = isPaid ? 'Settled' : 'Payment Due';
            const totalFmt = parseFloat(inv.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2});

            return `
                <div class="item-card" data-invoice-id="${inv.iD}">
                    <div class="item-header">
                        <span class="item-code">${inv.invoice_number}</span>
                        <span class="badge ${badgeClass}">${badgeLabel}</span>
                    </div>
                    <div style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 4px 0;">
                        $${totalFmt} <span style="font-size: 0.75rem; color: #64748b; font-weight: 500;">USD</span>
                    </div>
                    <div class="item-meta">
                        <span>Period: ${inv.billing_period_start} to ${inv.billing_period_end}</span>
                        <span>Due: ${inv.due_date}</span>
                    </div>
                </div>
            `;
        }).join('');

        container.querySelectorAll('.item-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.getAttribute('data-invoice-id');
                if (id) {
                    window.navigateTo('invoices/view', { id });
                }
            });
        });
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', () => {
            filterPills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            renderInvoices(pill.getAttribute('data-filter'));
        });
    });

    loadInvoices();
};
