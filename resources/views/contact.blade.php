<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RSMS • Contact</title>
        <link rel="icon" type="image/png" href="/emblem.png" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-[#1b1b18] min-h-screen w-full">
        <div id="app" class="w-full">
            <app-header></app-header>
        </div>

        <!-- Breadcrumb / Title bar -->
        <section class="bg-[#0AA74A] text-white">
            <div class="px-4 md:px-8 py-5">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold">Contact</h1>
                    <nav class="text-sm opacity-95">
                        <a href="/" class="hover:underline">Home</a>
                        <span class="mx-2">/</span>
                        <span>Contact</span>
                    </nav>
                </div>
            </div>
        </section>

        <!-- Map -->
        <section>
            <div class="px-0">
                <iframe title="Map" class="w-full h-[360px] md:h-[420px]" style="border:0" loading="lazy" allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15933.042908730497!2d39.233!3d-6.8007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sDar%20es%20Salaam!5e0!3m2!1sen!2stz!4v1700000000000"></iframe>
            </div>
        </section>

        <!-- Info cards -->
        <section class="px-4 md:px-8 -mt-10 relative z-10">
            <div class="bg-white rounded-md shadow-md border border-[#ecebea] p-6 max-w-screen-xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-[#E6FF8A] flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#0B6B3A]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s8-5.5 8-11a8 8 0 1 0-16 0c0 5.5 8 11 8 11Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.6"/></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold">Location:</h4>
                            <p class="text-[#6b6a67]">Mwanza HQ, Regional Education Offices</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-[#B3F2CF] flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#0A5F34]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.6"/></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold">Email:</h4>
                            <p class="text-[#6b6a67]"><a class="hover:underline" href="mailto:info@rsms.go.tz">info@rsms.go.tz</a></p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-[#CDE7FF] flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#0B6B3A]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6"/><path d="M8 6h8M12 19h0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold">Call:</h4>
                            <p class="text-[#6b6a67]"><a class="hover:underline" href="tel:+255710000000">+255 710 000 000</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact form -->
        <section class="px-4 md:px-8 py-8">
            <div class="bg-white rounded-md shadow-md border border-[#ecebea] p-6 max-w-screen-xl mx-auto">
                <form action="#" method="post" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" placeholder="Your Name" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                        <input type="email" placeholder="Your Email" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                    </div>
                    <input type="text" placeholder="Subject" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]" />
                    <textarea rows="6" placeholder="Message" class="w-full border border-[#e5e4e2] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0B6B3A]"></textarea>
                    <div class="flex justify-center">
                        <button type="button" class="inline-flex items-center justify-center px-8 py-3 rounded bg-[#1fb64f] text-white font-medium hover:bg-[#19a244]">Send Message</button>
                    </div>
                </form>
            </div>
        </section>

        <div id="app-footer">
            <footer-section></footer-section>
        </div>
    </body>
</html>
