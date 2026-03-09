<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-400 rounded-lg text-sm font-medium shadow-sm hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 disabled:opacity-50 transition !text-slate-900']) }}>
    {{ $slot }}
</button>
