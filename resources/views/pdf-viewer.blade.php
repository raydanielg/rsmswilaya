<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Viewer</title>
    @vite(['resources/css/app.css'])
    <style>
      html, body { height: 100%; }
    </style>
  </head>
  <body class="min-h-screen bg-[#0b0b0b] text-white">
    <div class="fixed top-0 inset-x-0 z-50 bg-[#0B6B3A] text-white border-b border-black/10">
      <div class="h-12 px-3 md:px-6 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <a href="javascript:history.back()" class="inline-flex items-center justify-center h-8 w-8 rounded hover:bg-white/10" title="Back" aria-label="Back">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
          </a>
          <div class="truncate max-w-[55vw] md:max-w-[60vw]">
            <div id="docTitle" class="text-sm md:text-base font-medium truncate">Document</div>
            <div id="docMeta" class="text-[11px] md:text-xs text-white/80 truncate"></div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <a id="openBtn" href="#" target="_blank" rel="noopener" class="hidden sm:inline-flex text-xs px-3 py-1.5 rounded bg-[#0AA74A] hover:bg-[#089543]">Open</a>
          <button id="printBtn" class="inline-flex text-xs px-3 py-1.5 rounded border border-white/20 hover:bg-white/10">Print</button>
        </div>
      </div>
    </div>

    <main class="pt-12 h-screen">
      <div class="relative h-[calc(100vh-48px)] bg-black">
        <iframe id="pdfFrame" class="absolute inset-0 w-full h-full" src="{{ $src }}#toolbar=0&navpanes=0&view=FitH" title="PDF" allowfullscreen></iframe>
      </div>
    </main>

    <script>
      (function(){
        const src = @json($src);
        function filenameFromUrl(u){ try{ const url = new URL(u, window.location.origin); const p = url.pathname.split('/'); return p[p.length-1] || 'document.pdf'; } catch(e){ return 'document.pdf'; } }
        function isExternal(u){ try{ const url = new URL(u, window.location.origin); return url.origin !== window.location.origin; } catch(e){ return false; } }
        const titleEl = document.getElementById('docTitle');
        const metaEl = document.getElementById('docMeta');
        const openBtn = document.getElementById('openBtn');
        const printBtn = document.getElementById('printBtn');
        const frame = document.getElementById('pdfFrame');
        const fname = filenameFromUrl(src);
        titleEl.textContent = fname;
        metaEl.textContent = isExternal(src) ? 'External document' : 'Local document';
        openBtn.href = src;
        printBtn.addEventListener('click', function(){ try{ frame.contentWindow.focus(); frame.contentWindow.print(); } catch(e){ window.open(src, '_blank'); } });
      })();
    </script>
  </body>
</html>
