/**
 * Posts calculator output that has already been calculated and downloads the
 * binary PDF response. It is framework-agnostic, so Alpine components can call
 * it directly as `exportPdf(payload)`.
 */
export async function exportPdf(payload, endpoint = '/generate-pdf') {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const response = await fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/pdf, application/json',
            'Content-Type': 'application/json',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        },
        body: JSON.stringify(payload),
    });

    if (!response.ok) {
        let message = 'PDF tidak dapat dibuat. Silakan coba lagi.';

        try {
            const error = await response.json();
            message = error.message || message;
        } catch (_) {
            // Keep the user-facing fallback when the response is not JSON.
        }

        throw new Error(message);
    }

    const blob = await response.blob();
    const disposition = response.headers.get('content-disposition') || '';
    const filename = disposition.match(/filename="?([^";]+)"?/i)?.[1] || 'nawatax-report.pdf';
    const url = URL.createObjectURL(blob);
    const anchor = document.createElement('a');

    anchor.href = url;
    anchor.download = filename;
    document.body.appendChild(anchor);
    anchor.click();
    anchor.remove();
    URL.revokeObjectURL(url);
}

window.exportPdf = exportPdf;
