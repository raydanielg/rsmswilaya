// PDF Download functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle download clicks
    const downloadLinks = document.querySelectorAll('a[download]');
    
    downloadLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const filename = this.getAttribute('download');
            
            // Show loading state
            const originalHTML = this.innerHTML;
            this.innerHTML = '<svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" opacity="0.3"/><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
            this.style.opacity = '0.7';
            
            // Reset after a short delay
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.style.opacity = '1';
            }, 2000);
        });
    });
    
    // Handle PDF preview
    const pdfLinks = document.querySelectorAll('a[href$=".pdf"]:not([download])');
    
    pdfLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Open PDF in new tab for better experience
            this.setAttribute('target', '_blank');
            this.setAttribute('rel', 'noopener noreferrer');
        });
    });
});
