<footer class="mt-32 relative bg-white dark:bg-slate-950
               border-t border-slate-200 dark:border-slate-800">

    {{-- Decorative Gradient Blur --}}
    <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b
                from-indigo-50/50 dark:from-indigo-900/10
                to-transparent pointer-events-none"></div>

    {{-- ================= TOP SECTION ================= --}}
    <div class="relative max-w-7xl mx-auto px-6 py-24">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-16">

            {{-- BRAND --}}
            <div class="lg:col-span-2">

                <h3 class="text-3xl font-extrabold tracking-tight
                           text-slate-900 dark:text-white">
                    Finance<span class="text-indigo-600">AI</span>
                </h3>

                <p class="mt-6 text-sm text-slate-500 dark:text-slate-400
                          leading-relaxed max-w-sm">
                    Enterprise-grade financial intelligence platform built for
                    families, professionals, and modern wealth builders.
                    Smart analytics. Predictive insights. Total clarity.
                </p>

                {{-- TRUST BADGES --}}
                <div class="mt-6 flex flex-wrap gap-3 text-xs">
                    <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800">
                        🔐 Bank-Level Security
                    </span>
                    <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800">
                        ⚡ AI Powered
                    </span>
                </div>

                {{-- SOCIAL --}}
                <div class="mt-8 flex gap-4">

                    @php
                        $socialClass = "h-10 w-10 flex items-center justify-center
                                        rounded-xl bg-slate-100 dark:bg-slate-800
                                        hover:bg-indigo-600 hover:text-white
                                        transition-all duration-300
                                        hover:scale-110 hover:shadow-lg";
                    @endphp

                    <a href="#" class="{{ $socialClass }}" aria-label="Twitter">
                        <i class="fa-brands fa-twitter"></i>
                    </a>

                    <a href="#" class="{{ $socialClass }}" aria-label="LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>

                    <a href="#" class="{{ $socialClass }}" aria-label="GitHub">
                        <i class="fa-brands fa-github"></i>
                    </a>

                </div>
            </div>


            {{-- PRODUCT --}}
            <div>
                <h4 class="footer-heading">Product</h4>
                <ul class="footer-links">

                    @if(Route::has('features'))
                        <li><a href="{{ route('features') }}">Features</a></li>
                    @endif

                    @if(Route::has('pricing'))
                        <li><a href="{{ route('pricing') }}">Pricing</a></li>
                    @endif

                    @if(Route::has('user.reports.index') && auth()->check())
                        <li><a href="{{ route('user.reports.index') }}">Reports</a></li>
                    @endif

                </ul>
            </div>


            {{-- COMPANY --}}
            <div>
                <h4 class="footer-heading">Company</h4>
                <ul class="footer-links">

                    @if(Route::has('about'))
                        <li><a href="{{ route('about') }}">About</a></li>
                    @endif

                    @if(Route::has('contact'))
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    @endif

                </ul>
            </div>


            {{-- LEGAL --}}
            <div>
                <h4 class="footer-heading">Legal</h4>
                <ul class="footer-links">

                    @if(Route::has('privacy'))
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    @endif

                    @if(Route::has('terms'))
                        <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                    @endif

                </ul>
            </div>

        </div>
    </div>


    {{-- ================= NEWSLETTER ================= --}}
    <div class="border-t border-slate-200 dark:border-slate-800
                bg-gradient-to-r from-slate-50 to-indigo-50
                dark:from-slate-900 dark:to-slate-950">

        <div class="max-w-7xl mx-auto px-6 py-14
                    flex flex-col md:flex-row items-center justify-between gap-8">

            <div>
                <h5 class="text-xl font-bold text-slate-900 dark:text-white">
                    Stay ahead financially
                </h5>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                    Get monthly AI-driven insights directly to your inbox.
                </p>
            </div>

            <form method="POST" action="#"
                  class="flex w-full md:w-auto gap-3">
                @csrf

                <input type="email"
                       required
                       placeholder="Enter your email"
                       class="w-full md:w-80 px-4 py-2.5 rounded-xl
                              border border-slate-300 dark:border-slate-700
                              bg-white dark:bg-slate-800
                              text-sm focus:ring-2 focus:ring-indigo-500
                              outline-none transition">

                <button type="submit"
                        class="px-6 py-2.5 rounded-xl
                               bg-indigo-600 text-white text-sm font-semibold
                               hover:bg-indigo-700
                               hover:shadow-lg hover:scale-[1.02]
                               transition-all duration-300">
                    Subscribe
                </button>

            </form>

        </div>
    </div>


    {{-- ================= BOTTOM STRIP ================= --}}
    <div class="border-t border-slate-200 dark:border-slate-800">

        <div class="max-w-7xl mx-auto px-6 py-8
                    flex flex-col md:flex-row items-center justify-between
                    text-xs text-slate-500 dark:text-slate-400">

            <p>
                © {{ now()->year }} FinanceAI. All rights reserved.
            </p>

            <p class="mt-4 md:mt-0">
                Built with precision • Powered by Artificial Intelligence
            </p>

        </div>
    </div>

</footer>


{{-- Footer Utility Classes --}}
<style>
.footer-heading {
    @apply text-xs font-bold uppercase tracking-widest
           text-slate-800 dark:text-slate-300 mb-6;
}

.footer-links li a {
    @apply text-sm text-slate-600 dark:text-slate-400
           hover:text-indigo-600 dark:hover:text-indigo-400
           transition duration-200;
}
</style>