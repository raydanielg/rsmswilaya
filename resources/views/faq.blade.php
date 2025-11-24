<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RSMS • FAQs</title>
        <link rel="icon" type="image/png" href="/emblem.png" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#F7F7F6] text-[#1b1b18] min-h-screen w-full">
        <div id="app" class="w-full">
            <app-header></app-header>

        <!-- Breadcrumb / Title bar -->
        <section class="bg-[#0AA74A] text-white">
            <div class="px-4 md:px-8 py-5">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold">FAQs</h1>
                    <nav class="text-sm opacity-95">
                        <a href="/" class="hover:underline">Home</a>
                        <span class="mx-2">/</span>
                        <span>FAQ</span>
                    </nav>
                </div>
            </div>
        </section>

        <!-- FAQ list -->
        <main class="px-4 md:px-8 py-10">
            <div class="max-w-screen-lg mx-auto space-y-4">
                <details class="group bg-white rounded-md shadow-sm border border-[#ecebea] p-5" open>
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <div class="flex items-center gap-2 text-[#0B6B3A] font-medium">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 8v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                            <span>What is RSMS and who is it for?</span>
                        </div>
                        <svg class="w-5 h-5 text-[#6b6a67] group-open:rotate-180 transition" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd"/></svg>
                    </summary>
                    <div class="mt-3 text-[#4f4e4b]">
                        RSMS (Results Management System) is a regional and district examination results platform serving
                        primary to secondary levels. It streamlines candidate registration, exam management, analytics,
                        and secure results delivery via web and mobile.
                    </div>
                </details>

                <details class="group bg-white rounded-md shadow-sm border border-[#ecebea] p-5">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <div class="flex items-center gap-2 text-[#0B6B3A] font-medium">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 8v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                            <span>Which levels and jurisdictions are supported?</span>
                        </div>
                        <svg class="w-5 h-5 text-[#6b6a67] group-open:rotate-180 transition" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd"/></svg>
                    </summary>
                    <div class="mt-3 text-[#4f4e4b]">
                        RSMS supports regional and district internal examinations from Primary School to Secondary School. It
                        accommodates both school candidates and private candidates depending on your administration settings.
                    </div>
                </details>

                <details class="group bg-white rounded-md shadow-sm border border-[#ecebea] p-5">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <div class="flex items-center gap-2 text-[#0B6B3A] font-medium">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 8v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                            <span>Can schools access analytics and historical trends?</span>
                        </div>
                        <svg class="w-5 h-5 text-[#6b6a67] group-open:rotate-180 transition" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd"/></svg>
                    </summary>
                    <div class="mt-3 text-[#4f4e4b]">
                        Yes. RSMS provides dashboards for schools and administrators to monitor performance, compare terms/years,
                        and drill into subjects and classes.
                    </div>
                </details>

                <details class="group bg-white rounded-md shadow-sm border border-[#ecebea] p-5">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <div class="flex items-center gap-2 text-[#0B6B3A] font-medium">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 8v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                            <span>How do parents and students get results?</span>
                        </div>
                        <svg class="w-5 h-5 text-[#6b6a67] group-open:rotate-180 transition" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd"/></svg>
                    </summary>
                    <div class="mt-3 text-[#4f4e4b]">
                        Results can be accessed from the web portal or via the RSMS mobile app once published by your regional/district
                        administrators. SMS delivery can be enabled where applicable.
                    </div>
                </details>
            </div>
        </main>
        <footer-section></footer-section>
        </div>
    </body>
</html>
