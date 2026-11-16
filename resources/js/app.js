// Livewire já inclui o Alpine.js (x-data, x-show, x-trap, x-anchor…) e injeta os scripts
// automaticamente. Use este arquivo para JS próprio do projeto.

/*
| Estatísticas do site: envia ao painel (Admin › Acessos) os cliques em links,
| botões e perguntas. Só roda nas páginas que têm <meta name="track-clicks">.
| Use data-track="Nome" num elemento para dar um rótulo fixo ao clique.
*/
const trackUrl = document.querySelector('meta[name="track-clicks"]')?.content;

if (trackUrl && navigator.sendBeacon) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    document.addEventListener(
        'click',
        (event) => {
            const el = event.target.closest?.('a, button, summary, [data-track]');
            if (!el) return;

            const label = el.dataset.track || el.getAttribute('aria-label') || el.innerText || el.getAttribute('title') || '';
            const area = el.closest('section[id], header, footer, nav');

            const data = new FormData();
            data.append('_token', token);
            data.append('label', label.replace(/\s+/g, ' ').trim().slice(0, 120));
            data.append('href', el.getAttribute('href') ?? '');
            data.append('section', area?.id || area?.tagName.toLowerCase() || '');
            data.append('element', el.tagName.toLowerCase());

            navigator.sendBeacon(trackUrl, data);
        },
        { capture: true, passive: true },
    );
}
