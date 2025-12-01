<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RSMS • About</title>
        <link rel="icon" type="image/png" href="/emblem.png" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-[#1b1b18] min-h-screen w-full" style="font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
        <div id="app" class="w-full">
            <app-header></app-header>
        </div>

        <!-- Breadcrumb / Title bar -->
        <section class="bg-[#0AA74A] text-white">
            <div class="px-4 md:px-8 py-5">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold">About</h1>
                    <nav class="text-sm opacity-95">
                        <a href="/" class="hover:underline">Home</a>
                        <span class="mx-2">/</span>
                        <span>About</span>
                    </nav>
                </div>
            </div>
        </section>

        <!-- Content -->
        <main class="px-4 md:px-8 py-10 max-w-screen-xl mx-auto">
            <article class="prose max-w-none">
                <h2 class="text-2xl font-semibold mb-3">Summary of RSMS</h2>
                <p>
                    RSMS (Results Management System) ni mfumo wa kidijitali uliobuniwa na kuanzishwa Mwanza ili kusimamia
                    na kuratibu mitihani ya ndani ya <strong>mikoa na wilaya</strong>. Mfumo unahudumia ngazi zote za elimu kuanzia
                    <strong>Shule ya Msingi</strong> hadi <strong>Sekondari</strong>, ukiwezesha uratibu wa usajili, ratiba, uendeshaji wa mitihani,
                    ukusanyaji wa majibu, uchambuzi wa matokeo na utoaji wa taarifa kwa wadau kwa wakati.
                </p>

                <h3 class="text-xl font-semibold mt-8 mb-2">The Establishment</h3>
                <p>
                    RSMS ulianzishwa ili kujibu changamoto za ukusanyaji wa taarifa, ucheleweshaji wa takwimu na ukosefu wa
                    uwazi katika mchakato wa matokeo. Kwa kuanzia Mwanza, mfumo umejengwa kuwa <em>modular</em> na <em>scalable</em> ili kuweza
                    kutumika katika maeneo mbalimbali ya nchi kwa urahisi.
                </p>

                <h3 class="text-xl font-semibold mt-8 mb-2">Objectives</h3>
                <ul class="list-disc pl-6">
                    <li>Kuboresha ufanisi wa usimamizi wa mitihani ya ndani ya mikoa na wilaya.</li>
                    <li>Kurahisisha usajili wa watahiniwa wa shule na binafsi.</li>
                    <li>Kutoa <strong>dashboards</strong> na <strong>analytics</strong> kwa shule na mamlaka husika kuona mwenendo wa ufaulu.</li>
                    <li>Kuwezesha upatikanaji wa matokeo kwa haraka kupitia wavuti na programu ya simu.</li>
                    <li>Kuhakikisha usalama, uadilifu na uharaka wa taarifa za mitihani.</li>
                </ul>

                <h3 class="text-xl font-semibold mt-8 mb-2">Scope & Coverage</h3>
                <p>
                    Mfumo unashughulikia michakato yote muhimu: uundaji wa mitihani, usambazaji, usahihishaji, uingizaji wa alama,
                    uhalilishaji wa takwimu na uzalishaji wa vyeti/vyithibitisho kwa ngazi zote kuanzia msingi hadi sekondari.
                </p>

                <h3 class="text-xl font-semibold mt-8 mb-2">Mobile App</h3>
                <p>
                    Programu ya simu ya RSMS inawawezesha wazazi, wanafunzi na walimu kufuatilia taarifa muhimu kama
                    ratiba, matangazo, na matokeo kwa wakati. Programu imeundwa kuwa nyepesi, salama na rahisi kutumia.
                </p>

                <h3 class="text-xl font-semibold mt-8 mb-2">Governance & Security</h3>
                <p>
                    RSMS inazingatia viwango vya usalama, udhibiti wa upatikanaji (RBAC) na <em>audit trail</em> ili kuhakikisha uadilifu
                    wa taarifa katika kila hatua ya mchakato wa mitihani.
                </p>
            </article>
        </main>

        <div id="app-footer">
            <footer-section></footer-section>
        </div>
    </body>
</html>
