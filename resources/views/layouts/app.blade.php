<!DOCTYPE html>
<html lang="en"
      x-data="layout()"
      x-init="init()"
      :class="{ 'dark': dark }"
      class="scroll-smooth">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title','FinanceAI Enterprise')</title>

{{-- Tailwind --}}
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
darkMode:'class',
theme:{
extend:{
colors:{
brand:'#6366f1'
},
boxShadow:{
soft:'0 20px 60px rgba(15,23,42,.08)',
ultra:'0 30px 120px rgba(15,23,42,.12)'
},
borderRadius:{
xl2:'22px'
}
}
}
}
</script>

{{-- Alpine --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- FontAwesome --}}
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@stack('styles')

<style>

/* background */

body{
background:#f8fafc;
transition:all .25s ease;
}

.dark body{
background:#020617;
}

/* content card */

.content-wrapper{
background:white;
border-radius:28px;
box-shadow:0 40px 120px rgba(15,23,42,.08);
transition:.3s;
}

.dark .content-wrapper{
background:#1e293b;
}

/* scroll container */

.scroll-container{
height:calc(100vh - 80px);
overflow-y:auto;
}

/* custom scrollbar */

::-webkit-scrollbar{
width:6px;
}

::-webkit-scrollbar-thumb{
background:#cbd5f5;
border-radius:10px;
}

.dark ::-webkit-scrollbar-thumb{
background:#334155;
}

/* sidebar animation */

.sidebar-animate{
transition:transform .25s ease;
}

/* loader */

#pageLoader{
position:fixed;
inset:0;
display:flex;
align-items:center;
justify-content:center;
background:white;
z-index:9999;
}

.dark #pageLoader{
background:#020617;
}

/* scroll progress */

#scrollProgress{
position:fixed;
top:0;
left:0;
height:3px;
background:#6366f1;
width:0%;
z-index:9999;
}

/* floating action */

.float-btn{
animation:float 6s ease-in-out infinite;
}

@keyframes float{
0%,100%{transform:translateY(0)}
50%{transform:translateY(-10px)}
}

</style>

</head>


<body class="text-slate-800 dark:text-slate-200 antialiased">

{{-- PAGE LOADER --}}
<div id="pageLoader">
<div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
</div>

{{-- SCROLL PROGRESS --}}
<div id="scrollProgress"></div>


{{-- NAVBAR --}}
@include('partials.navbar')


<div class="flex min-h-screen">


{{-- SIDEBAR --}}
<aside
class="sidebar-animate fixed lg:static
inset-y-0 left-0
w-72
bg-white dark:bg-slate-900
border-r border-slate-200 dark:border-slate-800
z-40
transform"
:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

@include('partials.sidebar')

</aside>



{{-- OVERLAY --}}
<div
x-show="sidebarOpen"
x-transition.opacity
@click="sidebarOpen=false"
class="fixed inset-0 bg-black/40 lg:hidden z-30">
</div>



{{-- MAIN --}}
<div class="flex-1 flex flex-col">


<main class="flex-1 p-6 md:p-12 scroll-container">

<div class="max-w-[1700px] mx-auto">


<div class="content-wrapper p-8 md:p-14">

@yield('content')

</div>


</div>

</main>


</div>


</div>


{{-- FLOATING QUICK ACTION --}}
<div class="fixed bottom-8 right-8 float-btn">

<button
class="w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg hover:scale-105 transition">

<i class="fa-solid fa-bolt"></i>

</button>

</div>



{{-- ENTERPRISE CORE SCRIPT --}}
<script>

function layout(){

return{

dark:false,
sidebarOpen:false,

init(){

this.dark=
localStorage.getItem('dark')==='true'
|| window.matchMedia('(prefers-color-scheme:dark)').matches

this.$watch('dark',value=>{
localStorage.setItem('dark',value)
})

/* loader */

window.addEventListener("load",()=>{
document.getElementById("pageLoader").style.display="none"
})

/* scroll progress */

window.addEventListener("scroll",()=>{

let scrollTop=document.documentElement.scrollTop
let height=document.documentElement.scrollHeight-document.documentElement.clientHeight
let scrolled=(scrollTop/height)*100

document.getElementById("scrollProgress").style.width=scrolled+"%"

})

/* keyboard shortcut */

document.addEventListener("keydown",(e)=>{

if(e.key==="b"){
this.sidebarOpen=!this.sidebarOpen
}

})

}

}

}

</script>


@stack('scripts')

</body>
</html>