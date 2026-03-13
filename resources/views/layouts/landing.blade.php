<!DOCTYPE html>
<html lang="en" class="scroll-smooth" id="htmlRoot">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>@yield('title', config('app.name','FinanceAI'))</title>

<meta name="description" content="@yield('meta_description','FinanceAI - Enterprise-grade financial intelligence platform.')">
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta property="og:title" content="@yield('title', config('app.name'))">
<meta property="og:description" content="@yield('meta_description','FinanceAI SaaS Platform')">
<meta property="og:type" content="website">

{{-- Prevent dark mode flicker --}}
<script>
if(localStorage.getItem('theme') === 'dark'){
document.documentElement.classList.add('dark');
}
</script>

{{-- Fonts --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

{{-- Tailwind --}}
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
darkMode:'class',
theme:{
extend:{
fontFamily:{sans:['Inter','sans-serif']},
colors:{brand:'#4f46e5'}
}
}
}
</script>

@stack('styles')

<style>

/* Loader */
#loader{
position:fixed;
inset:0;
background:white;
display:flex;
align-items:center;
justify-content:center;
z-index:9999;
}

.dark #loader{
background:#020617;
}

/* animated gradient */
.animate-gradient{
background-size:200% 200%;
animation:gradient 8s ease infinite;
}

@keyframes gradient{
0%{background-position:0% 50%}
50%{background-position:100% 50%}
100%{background-position:0% 50%}
}

/* navbar scroll */
.navbar-scrolled{
backdrop-filter:blur(20px);
box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* mobile menu */
#mobileMenu{
transition:all .3s ease;
}

/* floating button animation */
.float-btn{
animation:float 6s ease-in-out infinite;
}

@keyframes float{
0%,100%{transform:translateY(0)}
50%{transform:translateY(-10px)}
}

</style>

</head>

<body class="bg-white dark:bg-slate-950 text-slate-900 dark:text-white transition-colors duration-300">

{{-- ================= PAGE LOADER ================= --}}
<div id="loader">
<div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
</div>


{{-- ================= SCROLL PROGRESS ================= --}}
<div id="progressBar"
class="fixed top-0 left-0 h-1 bg-indigo-600 w-0 z-[9999]"></div>


{{-- ================= NAVBAR ================= --}}
<nav id="navbar"
class="fixed top-0 w-full z-50
bg-white/80 dark:bg-slate-900/80
border-b border-slate-200 dark:border-slate-800
transition-all duration-300">

<div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

<a href="{{ route('home') }}"
class="text-2xl font-extrabold text-indigo-600">
FinanceAI
</a>

{{-- Desktop menu --}}
<div class="hidden md:flex items-center gap-8 text-sm font-medium">

@php $current = Route::currentRouteName(); @endphp

@foreach(['features','pricing','about','contact'] as $route)

@if(Route::has($route))
<a href="{{ route($route) }}"
class="{{ $current==$route ? 'text-indigo-600 font-semibold' : 'hover:text-indigo-600' }}">
{{ ucfirst($route) }}
</a>
@endif

@endforeach


{{-- Dark mode toggle --}}
<button onclick="toggleDark()"
class="w-9 h-9 flex items-center justify-center rounded-lg border
border-slate-200 dark:border-slate-700
hover:bg-slate-100 dark:hover:bg-slate-800">

<span id="themeIcon">🌙</span>

</button>


@guest
<a href="{{ route('login') }}"
class="px-4 py-2 rounded-xl border">
Login
</a>

<a href="{{ route('register') }}"
class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-semibold">
Get Started
</a>
@endguest

</div>


{{-- Mobile button --}}
<button onclick="toggleMenu()" class="md:hidden text-2xl">
☰
</button>

</div>


{{-- Mobile Menu --}}
<div id="mobileMenu"
class="hidden md:hidden bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">

<div class="px-6 py-4 space-y-3">

<a href="{{ route('features') }}" class="block">Features</a>
<a href="{{ route('pricing') }}" class="block">Pricing</a>
<a href="{{ route('about') }}" class="block">About</a>
<a href="{{ route('contact') }}" class="block">Contact</a>

@guest
<a href="{{ route('login') }}" class="block">Login</a>
<a href="{{ route('register') }}" class="block text-indigo-600 font-semibold">Register</a>
@endguest

</div>

</div>

</nav>


{{-- ================= MAIN ================= --}}
<main class="pt-28 min-h-screen">

@yield('content')

</main>


{{-- ================= CTA ================= --}}
<section class="bg-gradient-to-r from-indigo-600 to-purple-600 py-20 mt-20 text-white text-center">

<div class="max-w-4xl mx-auto px-6">

<h2 class="text-3xl md:text-4xl font-extrabold">
Ready to take control of your finances?
</h2>

<p class="mt-4 opacity-90">
Join FinanceAI and experience intelligent financial automation.
</p>

<div class="mt-8">

<a href="{{ route('register') }}"
class="px-8 py-3 bg-white text-indigo-700 rounded-xl font-bold hover:scale-105 transition">
Get Started Free
</a>

</div>

</div>

</section>


{{-- ================= FOOTER ================= --}}
<footer class="bg-slate-100 dark:bg-slate-900 border-t dark:border-slate-800 py-14 mt-20">

<div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-10 text-sm">

<div>
<h4 class="font-bold text-lg text-indigo-600">FinanceAI</h4>
<p class="mt-3 text-slate-500 dark:text-slate-400">
Enterprise-grade financial intelligence platform.
</p>
</div>

<div>
<h5 class="font-semibold mb-3">Product</h5>
<ul class="space-y-2">
<li><a href="{{ route('features') }}">Features</a></li>
<li><a href="{{ route('pricing') }}">Pricing</a></li>
</ul>
</div>

<div>
<h5 class="font-semibold mb-3">Company</h5>
<ul class="space-y-2">
<li><a href="{{ route('about') }}">About</a></li>
<li><a href="{{ route('contact') }}">Contact</a></li>
</ul>
</div>

<div>
<h5 class="font-semibold mb-3">Legal</h5>
<ul class="space-y-2">
<li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
<li><a href="{{ route('terms') }}">Terms</a></li>
</ul>
</div>

</div>

<div class="text-center mt-10 text-xs text-slate-400">
© {{ date('Y') }} FinanceAI
</div>

</footer>


{{-- ================= SCROLL TOP ================= --}}
<button id="scrollTop"
class="fixed bottom-6 right-6 bg-indigo-600 text-white w-11 h-11 rounded-full shadow-lg hidden float-btn">

↑

</button>


{{-- ================= SCRIPTS ================= --}}
<script>

/* Page loader */

window.addEventListener("load",()=>{
document.getElementById("loader").style.display="none"
})

/* Dark mode */

function toggleDark(){

const html=document.documentElement

html.classList.toggle("dark")

localStorage.setItem(
"theme",
html.classList.contains("dark")?"dark":"light"
)

}


/* Mobile menu */

function toggleMenu(){

document.getElementById("mobileMenu")
.classList.toggle("hidden")

}


/* Scroll progress */

window.addEventListener("scroll",()=>{

let scrollTop=document.documentElement.scrollTop
let height=document.documentElement.scrollHeight-document.documentElement.clientHeight

let scrolled=(scrollTop/height)*100

document.getElementById("progressBar").style.width=scrolled+"%"

})


/* Navbar shadow */

window.addEventListener("scroll",()=>{

const nav=document.getElementById("navbar")

if(window.scrollY>20){

nav.classList.add("navbar-scrolled")

}else{

nav.classList.remove("navbar-scrolled")

}

})


/* Scroll top */

window.addEventListener("scroll",()=>{

const btn=document.getElementById("scrollTop")

btn.classList.toggle("hidden",window.scrollY<400)

})

document.getElementById("scrollTop").onclick=()=>{
window.scrollTo({top:0,behavior:"smooth"})
}

</script>

@stack('scripts')

</body>
</html>