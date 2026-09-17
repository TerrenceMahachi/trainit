window.init = async function(data) {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const invId = data ? data.id : null;
    if (!invId) {
        window.navigateTo('invoices/list');
        return;
    }

    const backBtn = document.getElementById('btn-back-to-invoices');
    const invNumEl = document.getElementById('inv-number');
    const statusBadgeEl = document.getElementById('inv-status-badge');
    const clientNameEl = document.getElementById('inv-client-name');
    const legalNameEl = document.getElementById('inv-legal-name');
    const taxNumEl = document.getElementById('inv-tax-number');
    const dueDateEl = document.getElementById('inv-due-date');
    const issueDateEl = document.getElementById('inv-issue-date');
    const itemsContainer = document.getElementById('inv-items-container');
    const subtotalEl = document.getElementById('inv-subtotal');
    const vatEl = document.getElementById('inv-vat');
    const totalEl = document.getElementById('inv-total');
    const refCodeEl = document.getElementById('inv-ref-code');

    if (backBtn) {
        backBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.navigateTo('invoices/list');
        });
    }

    try {
        const response = await fetch(`${window.API_BASE}/api/mobile/invoices/view/${invId}?user_id=${user.id}`, {
            method: 'GET',
            credentials: 'include'
        });
        const res = await response.json();

        if (res.status === 1 && res.invoice) {
            const inv = res.invoice;
            const isPaid = parseInt(inv.payment_status, 10) === 2;

            if (invNumEl) invNumEl.textContent = inv.invoice_number;
            if (refCodeEl) refCodeEl.textContent = inv.invoice_number;
            if (clientNameEl) clientNameEl.textContent = inv.client_name;
            if (legalNameEl) legalNameEl.textContent = inv.legal_name || '';
            if (taxNumEl) taxNumEl.textContent = inv.tax_number || 'BP20098177';
            if (dueDateEl) dueDateEl.textContent = inv.due_date;
            if (issueDateEl) issueDateEl.textContent = inv.issue_date;

            if (statusBadgeEl) {
                statusBadgeEl.textContent = isPaid ? 'Settled' : 'Payment Due';
                statusBadgeEl.className = `badge ${isPaid ? 'badge-green' : 'badge-red'}`;
            }

            const sub = parseFloat(inv.subtotal || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
            const vat = parseFloat(inv.vat_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
            const tot = parseFloat(inv.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2});

            if (subtotalEl) subtotalEl.textContent = `$${sub}`;
            if (vatEl) vatEl.textContent = `$${vat}`;
            if (totalEl) totalEl.textContent = `$${tot}`;

            // Render items
            if (res.items && itemsContainer) {
                itemsContainer.innerHTML = res.items.map(item => `
                    <div style="padding: 8px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-main); line-height: 1.3;">${item.description}</div>
                            <div style="font-size: 0.72rem; color: #64748b;">Qty: ${item.quantity} × $${parseFloat(item.unit_price).toFixed(2)}</div>
                        </div>
                        <div style="font-weight: 700; color: var(--text-main); white-space: nowrap;">
                            $${parseFloat(item.total_price).toLocaleString('en-US', {minimumFractionDigits: 2})}
                        </div>
                    </div>
                `).join('');
            }
        }
    } catch (err) {
        console.error('Error fetching invoice:', err);
        window.showToast('Error loading invoice details', 'error');
    }
};
