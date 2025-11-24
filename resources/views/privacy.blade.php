<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Privacy Policy • RSMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
  <body class="bg-white text-[#1b1b18] min-h-screen w-full">
    <div id="app" class="w-full min-h-screen flex flex-col">
      <app-header></app-header>

      <!-- Title bar -->
      <section class="bg-[#0AA74A] text-white">
        <div class="px-4 md:px-8 py-5 flex items-center justify-between">
          <h1 class="text-2xl font-semibold">Privacy Policy</h1>
          <nav class="text-sm opacity-95">
            <a href="/" class="hover:underline">Home</a>
            <span class="mx-2">/</span>
            <span>Privacy Policy</span>
          </nav>
        </div>
      </section>

      <!-- Content -->
      <main class="px-4 md:px-8 py-8 flex-1">
        <div class="max-w-screen-xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
          <article class="lg:col-span-8 space-y-6">
            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Introduction</h2>
              <p class="text-[#4f4e4b]">This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use the RSMS website and related services.</p>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Information We Collect</h2>
              <ul class="list-disc pl-6 space-y-2 text-[#4f4e4b]">
                <li>Usage data (pages visited, device/browser type, approximate location).</li>
                <li>Form submissions you provide (e.g., contact requests).</li>
                <li>Technical logs for security and performance monitoring.</li>
              </ul>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">How We Use Information</h2>
              <ul class="list-disc pl-6 space-y-2 text-[#4f4e4b]">
                <li>To operate and improve the RSMS site and services.</li>
                <li>To respond to support or contact requests.</li>
                <li>To uphold security, prevent abuse, and comply with legal obligations.</li>
              </ul>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Cookies & Analytics</h2>
              <p class="text-[#4f4e4b]">We may use cookies and aggregated analytics to understand usage and improve user experience. You can control cookies through your browser settings.</p>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Data Sharing</h2>
              <p class="text-[#4f4e4b]">We do not sell personal data. We may share limited information with service providers under confidentiality where necessary to operate the service, or when required by law.</p>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Data Retention</h2>
              <p class="text-[#4f4e4b]">We retain information only for as long as necessary for the purposes described in this policy or as required by law.</p>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Your Choices</h2>
              <ul class="list-disc pl-6 space-y-2 text-[#4f4e4b]">
                <li>Control cookies in your browser settings.</li>
                <li>Contact us to request access, correction, or deletion where applicable.</li>
              </ul>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Security</h2>
              <p class="text-[#4f4e4b]">We employ reasonable administrative, technical, and physical safeguards to protect information. No method of transmission or storage is 100% secure.</p>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Updates to This Policy</h2>
              <p class="text-[#4f4e4b]">We may update this policy periodically. Material changes will be highlighted on this page with an updated effective date.</p>
            </section>

            <section class="bg-white rounded-lg border border-[#ecebea] p-6">
              <h2 class="text-xl font-semibold mb-2">Contact</h2>
              <p class="text-[#4f4e4b]">If you have questions about this Privacy Policy, please contact us via the details provided on the Contact page.</p>
            </section>
          </article>

          <aside class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-lg border border-[#ecebea] p-5">
              <h3 class="text-lg font-semibold mb-2">Effective Date</h3>
              <p class="text-[#4f4e4b]">{{ now()->format('F d, Y') }}</p>
            </div>
            <div class="bg-white rounded-lg border border-[#ecebea] p-5">
              <h3 class="text-lg font-semibold mb-2">Short Summary</h3>
              <p class="text-[#4f4e4b]">We collect limited usage and submission data to operate and improve services, do not sell data, and share only as needed or required by law.</p>
            </div>
          </aside>
        </div>
      </main>

      <footer-section></footer-section>
    </div>
  </body>
</html>
